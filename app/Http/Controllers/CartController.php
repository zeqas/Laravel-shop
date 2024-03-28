<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\CartProduct;
use App\Service\CartService;

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
     *
     * @response scenario=success status=201 {
     *  "id": 1,
     *  "user_id": 1,
     *  "cart_product": {
     *   {
     *   "id": 1,
     *   "product_id": 1,
     *   "quantity": 1
     *   },
     *   {
     *    "id": 2,
     *    "product_id": 2,
     *    "quantity": 2
     *   }
     * }
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = auth()->user()->id;

        // 建立一個購物車
        // TODO: updateOrCreate? findOrCreate?
        $cart = Cart::firstOrCreate([
            'user_id' => $userId,
        ]);

        // 建立被放入的產品 cartProduct
        // 如果 cartProduct 已經存在，則更新數量
        $cartProductQuery = CartProduct::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $validatedData['product_id']);

        $isCartProductExist = $cartProductQuery->exists();
        $cartProduct = $cartProductQuery->first();

        // 如果有的話把對應的數量加到舊的 cartProduct
        if ($isCartProductExist) {
            $cartProduct->quantity += $validatedData['quantity'];
            $cartProduct->save();
        } else {
            // 否則建立一個 cartProduct
            $cartProduct = CartProduct::create([
                'cart_id' => $cart->id,
                'product_id' => $validatedData['product_id'],
                'quantity' => $validatedData['quantity'],
            ]);
        }

        // 回傳 cartProduct，即這次被放入的產品
        return response()->json($cartProduct, 201);
    }

    /**
     * 購物車 列表
     */
    public function show(Request $request)
    {
        $userId = auth()->user()->id;

        // 顯示 Cart 裏所有 CartProduct 的資料
        $cartProducts = CartProduct::query()->where('cart_id', $userId)->get();
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
    public function update(Request $request, $cartProductId)
    {
        $validatedData = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartProduct = CartProduct::query()->findOrFail($cartProductId);

        $cartProduct->update($validatedData);

        return response()->json($cartProduct, 201);
    }

    /**
     * 刪除購物車中的特定產品
     * @bodyParam id integer required 產品ID. Example: 1
     */
    public function destroy(int $cartId, int $productId)
    {
        CartProduct::query()
            ->where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->delete();

        return response(null, 204);
    }

    /**
     * 購物車結帳
     *
     */
    public function checkout(Request $request)
    {
        //TODO: ACID transaction
        $userId = auth()->user()->id;

        //檢查庫存是否足夠
        $cartProducts = CartProduct::query()->where('cart_id', $userId)->get();
        $total = 0;

        foreach ($cartProducts as $cartProduct) {
            $product = $cartProduct->product;
            if ($product->stock < $cartProduct->quantity) {
                return response()->json([
                    'message' => '庫存不足',
                ], 400);
            }

            $product->stock -= $cartProduct->quantity;
            $total += $this->cartService->calculatePrice($cartProduct);
        }

        return response()->json([
            'message' => '結帳成功，總共 $' . $total . ' 元',
        ], 201);
    }
}
