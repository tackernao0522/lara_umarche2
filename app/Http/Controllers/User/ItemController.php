<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PrimaryCategory;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');

        $this->middleware(function ($request, $next) {
            $id = $request->route()->parameter('item');
            if (!is_null($id)) {
                $itemId = Product::availableItems()
                    ->where('products.id', $id)
                    ->exists();
                if (!$itemId) {
                    abort(404);
                }
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // dd($request);

        $categories = PrimaryCategory::with('secondaries')->get();

        $products = Product::availableItems()
            ->selectCategory($request->category ?? '0')
            ->searchKeyword($request->keyword ?? '')
            ->sortOrder($request->sort)
            ->paginate($request->pagination ?? '20');
            // dd($request);

        // $stocks = DB::table('t_stocks')
        //     ->select('product_id', DB::raw('sum(quantity) as quantity'))
        //     ->groupBy('product_id')
        //     ->having('quantity', '>=', 1);

        // $products = DB::table('products')
        //     ->joinSub($stocks, 'stock', function ($join) {
        //         $join->on('products.id', '=', 'stock.product_id');
        //     })
        //     ->join('shops', 'products.shop_id', 'shops.id')
        //     ->join(
        //         'secondary_categories',
        //         'products.secondary_category_id',
        //         '=',
        //         'secondary_categories.id'
        //     )
        //     ->join('images as image1', 'products.image1', '=', 'image1.id')
        //     ->join('images as image2', 'products.image1', '=', 'image2.id')
        //     ->join('images as image3', 'products.image1', '=', 'image3.id')
        //     ->join('images as image4', 'products.image1', '=', 'image4.id')
        //     ->where('shops.is_selling', true)
        //     ->where('products.is_selling', true)
        //     ->select(
        //         'products.id',
        //         'products.name as name',
        //         'products.price',
        //         'products.sort_order as sort_order',
        //         'products.information',
        //         'secondary_categories.name as category',
        //         'image1.filename as filename'
        //     )
        //     ->get();

        // dd($stocks, $products);

        // $products = Product::all();

        return view('user.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        $quantity = Stock::where('product_id', $product->id)->sum('quantity');

        if ($quantity > 9) {
            $quantity = 9;
        }

        return view('user.show', compact('product', 'quantity'));
    }
}
