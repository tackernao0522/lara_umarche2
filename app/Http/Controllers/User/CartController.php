<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\SendOrderedMail;
use App\Jobs\SendThanksMail;
use App\Models\Cart;
use App\Models\Stock;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $products = $user->products;
        $totalPrice = 0;

        foreach ($products as $product) {
            $totalPrice += $product->price * $product->pivot->quantity;
        }

        // dd($products, $totalPrice);

        return view('user.cart', compact('products', 'totalPrice'));
    }

    public function add(Request $request)
    {
        // dd($request);
        $itemInCart = Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($itemInCart) {
            $itemInCart->quantity  += $request->quantity;
            $itemInCart->save();
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('user.cart.index');
    }

    public function delete($id)
    {
        Cart::where('product_id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('user.cart.index');
    }

    public function checkout()
    {
        $user = User::findOrFail(Auth::id());
        $products = $user->products;
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));

        $lineItems = [];
        foreach ($products as $product) {
            $quantity = '';
            $quantity = Stock::where('product_id', $product->id)->sum('quantity');

            if ($product->pivot->quantity > $quantity) {
                return redirect()->route('user.cart.index');
            } else {
                $stripe_products = $stripe->products->create([
                    'name' => $product->name,
                    'description' => $product->information,
                ]);
                $stripe_price = $stripe->prices->create([
                    'product' => $stripe_products,
                    'unit_amount' => $product->price,
                    'currency' => 'jpy',
                ]);
                $lineItem = [
                    'price' => $stripe_price,
                    'quantity' => $product->pivot->quantity,
                ];
                array_push($lineItems, $lineItem);
            }
        }
        // dd($lineItems);
        // ストライプに渡す前に在庫を減らす
        foreach ($products as $product) {
            Stock::create([
                'product_id' => $product->id,
                'type' => \Constant::PRODUCT_LIST['reduce'],
                'quantity' => $product->pivot->quantity * -1,
            ]);
        }

        $session = $stripe->checkout->sessions->create([
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'success_url' => route('user.cart.success'),
            'cancel_url' => route('user.cart.cancel'),
        ]);

        $publicKey = env('STRIPE_PUBLIC_KEY');

        return view('user.checkout', compact('session', 'publicKey'));
    }

    public function success()
    {
        $items = Cart::where('user_id', Auth::id())->get();
        $products = CartService::getItemsInCart($items);
        $user = User::findOrFail(Auth::id());

        SendThanksMail::dispatch($products, $user);
        foreach ($products as $product) {
            SendOrderedMail::dispatch($product, $user);
        }
        // dd('ユーザーメール送信テスト');

        Cart::where('user_id', Auth::id())->delete();

        return redirect()->route('user.items.index');
    }

    public function cancel()
    {
        $user = User::findOrFail(Auth::id());

        foreach ($user->products as $product) {
            Stock::create([
                'product_id' => $product->id,
                'type' => \Constant::PRODUCT_LIST['add'],
                'quantity' => $product->pivot->quantity,
            ]);
        }

        return redirect()->route('user.cart.index');
    }
}
