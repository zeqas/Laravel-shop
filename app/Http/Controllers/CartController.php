<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\CartProduct;
use App\Service\CartService;
use App\Http\Resources\CartResource;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

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
     * 新增商品到購物車
     * 回傳被新增的商品 id 和數量
     * @bodyParam product_id integer required 商品ID. Example: 1
     * @bodyParam quantity integer required 限制0以上. Example: 1
     *
     * @response scenario=success status=201 {
     *    "product_id": 1,
     *    "quantity": 1,
     *    "product": {
     *        "id": "1",
     *        "price": "100",
     *        "name": "Apple",
     *        "stock": 10
     *    }
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
        $cart = Cart::firstOrCreate([
            'user_id' => $userId,
        ]);

        // 建立被放入的商品 cartProduct
        // 如果 cartProduct 已經存在，則更新數量
        $cartProductQuery = CartProduct::query()
            ->where('cart_id', $cart->id)
            ->with('product')
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

        // 回傳 cartProduct，即這次被放入的商品
        return response()->json(new CartResource($cartProduct), 201);
    }

    /**
     * 購物車 列表
     * @response scenario=success status=201 {
     *   "cartProducts": [
     *       {
     *           "product_id": 1,
     *           "quantity": 1,
     *           "product": {
     *               "id": "1",
     *               "price": "100",
     *               "name": "Apple",
     *               "stock": 10
     *           }
     *       },
     *       {
     *           "product_id": 2,
     *           "quantity": 2,
     *           "product": {
     *               "id": 2,
     *               "price": "200",
     *               "name": "Banana",
     *               "stock": 10
     *           }
     *       }
     *   ],
     *   "total": 300
     * }
     */
    public function show(Request $request)
    {
        $userId = auth()->user()->id;

        // 取得使用者的購物車(目前每個使用者只有一個購物車)
        $cartId = Cart::query()->where('user_id', $userId)->value('id');
        // 顯示 Cart 裏所有 CartProduct 的資料
        $cartProducts = CartProduct::query()->where('cart_id', $cartId)->get();
        // 商品總價
        $total = 0;

        foreach ($cartProducts as $cartProduct) {
            $total += $this->cartService->calculatePrice($cartProduct);
        }

        return response()->json([
            'cartProducts' => CartResource::collection($cartProducts),
            'total' => $total,
        ], 201);
    }

    /**
     * 更新商品數量
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
     * 刪除購物車中的特定商品
     * @bodyParam id integer required 商品ID. Example: 1
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
     * 清空購物車
     * @bodyParam id integer required 購物車ID. Example: 1
     */
    public function clear(int $cartId)
    {
        $authenticated = $this->cartService->checkCartOwner($cartId);

        if (!$authenticated) {
            return response()->json([
                'message' => '無權限操作此購物車',
            ], 403);
        }

        CartProduct::query()
            ->where('cart_id', $cartId)
            ->delete();

        return response(null, 204);
    }

    /**
     * 購物車結帳
     * @response scenario=success status=201 {
     *  "message": "結帳成功，總共 $300 元"
     * }
     */
    public function checkout(Request $request)
    {
        DB::beginTransaction();
        $userId = auth()->user()->id;

        //檢查庫存是否足夠
        $cartId = Cart::query()->where('user_id', $userId)->value('id');
        $cartProducts = CartProduct::query()->where('cart_id', $cartId)->get();
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

            $product->save();
        }

        // 建立訂單 Order，將購物車內容轉換成訂單內容
        Order::create([
            'user_id' => $userId,
            'product_data' => CartResource::collection($cartProducts),
            'total' => $total,
        ]);

        // 刪除購物車內容
        foreach ($cartProducts as $cartProduct) {
            $cartProduct->delete();
        }

        DB::commit();

        return response()->json([
            'message' => '結帳成功，總共 $' . $total . ' 元，訂單建立成功',
        ], 201);
    }
}
