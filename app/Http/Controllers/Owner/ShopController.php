<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadImageRequest;
use App\Models\Shop;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use InterventionImage;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:owners');

        $this->middleware(function ($request, $next) {
            // dd($request->route()->parameter('shop')); // 文字列
            // dd(Auth::id()); // 数字
            // return $next($request);
            $id = $request->route()->parameter('shop'); // shopのid取得
            if (!is_null($id)) {
                // null判定
                $shopsOwnerId = Shop::findOrFail($id)->owner->id;
                $shopId = (int) $shopsOwnerId; // キャスト 文字列->数値に型変換
                $ownerId = Auth::id();
                if ($shopId !== $ownerId) {
                    // 同じでなかったら
                    abort(404); // 404画面
                }
            }

            return $next($request);
        });
    }

    public function index()
    {
        // phpinfo();
        // $ownerId = Auth::id(); // ログインしているオーナーのID
        // $shops = Shop::where('owner_id', $ownerId)->get();
        $shops = Shop::where('owner_id', Auth::id())->get(); // 1行で書ける

        return view('owner.shops.index', compact('shops'));
    }

    public function edit($id)
    {
        // dd(Shop::findOrFail($id));
        $shop = Shop::findOrFail($id);

        return view('owner.shops.edit', compact('shop'));
    }

    public function update(UploadImageRequest $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'information' => 'required|string|max:1000',
            'is_selling' => 'required',
        ], [
            'information.required' => '店舗情報は必須です。',
        ]);

        $imageFile = $request->image;

        // もし空でなかったら及びアップロードできたら
        if (!is_null($imageFile) && $imageFile->isValid()) {
            // Storage::putFile('public/shops', $imageFile); // リサイズなしの場合
            // $fileName = uniqid(rand() . '_');
            // $extension = $imageFile->extension();
            // $fileNameToStore = $fileName . '.' . $extension;
            // $resizedImage = InterventionImage::make($imageFile)
            //     ->resize(1920, 1080)
            //     ->encode();
            // // dd($imageFile, $resizedImage);

            // Storage::put('public/shops/' . $fileNameToStore, $resizedImage);
            $fileNameToStore = ImageService::upload($imageFile, 'shops');
        }

        $shop = Shop::findOrFail($id);
        $shop->name = $request->name;
        $shop->information = $request->information;
        $shop->is_selling = $request->is_selling;
        if (!is_null($imageFile) && $imageFile->isValid()) {
            $shop->filename = $fileNameToStore;
        }

        $shop->save();

        return redirect()
            ->route('owner.shops.index')
            ->with([
                'message' => '店舗情報を更新しました。',
                'status' => 'info',
            ]);
    }
}
