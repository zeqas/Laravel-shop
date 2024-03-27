<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartProduct;

/**
 * @group 購物車 management
 *
 * 購物車管理，包括送出、更新、刪除、計算
 */
class CartController extends Controller
{
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * 新增產品到購物車
     * @bodyParam product_id integer required 產品ID. Example: 1
     * @bodyParam quantity integer required 限制0以上. Example: 1
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = auth()->user->id;

        // 建立一個購物車與被放入的產品
        // TODO: updateOrCreate? findOrCreate?
        $cart = Cart::firstOrCreate([
            'user_id' => $userId,
        ]);

        // 建立一個 cartProduct
        $cartProduct = CartProduct::query()
            ->where('user_id', $userId)
            ->where('product_id', $validatedData['product_id'])
            ->first();

        // 如果有的話把對應的數量加到舊的 cartProduct
        if ($cartProduct) {
            $cartProduct->quantity += $validatedData['quantity'];
            $cartProduct->save();
        } else {
            $cartProduct = CartProduct::create([
                'user_id' => $userId,
                'product_id' => $validatedData['product_id'],
                'quantity' => $validatedData['quantity'],
            ]);
        }

        //FIXME: 需要儲存 cart?
        $cart->save();

        return response()->json($cartProduct, 201);
    }

    /**
     * 購物車 列表
     */
    public function show(Request $request)
    {
        $userId = auth()->user->id;

        // 顯示 Cart 裏所有 CartProduct 的資料
        $cartProducts = CartProduct::query()->where('user_id', $userId)->get();
        $total = 0;
        foreach ($cartProducts as $cartProduct) {
            $total += $this->cartService->calculatePrice($cartProduct);
        }

        return response()->json([
            'cartProducts' => $cartProducts,
            'total' => $total,
        ], 201);
    }

    /**
     * 更新產品數量
     * @bodyParam quantity integer 限制0以上
     */
    public function update(Request $request, CartProduct $cartProduct)
    {
        $validatedData = $request->validate([
            'quantity' => 'integer|min:0',
        ]);

        $cartProduct->update($validatedData);

        return response()->json($cartProduct, 201);
    }

    /**
     * 刪除產品
     * @bodyParam id integer required 產品ID. Example: 1
     */
    public function destroy(string $id)
    {
        $product = CartProduct::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }

    /**
     * 購物車結帳
     *
     */
    public function checkout(Request $request)
    {
        //TODO: ACID transaction
        $userId = auth()->user->id;

        //TODO: 檢查庫存是否足夠

    }
}
