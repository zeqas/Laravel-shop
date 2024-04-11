<?php

namespace App\Service;

use App\Exceptions\ForbiddenException;
use App\Models\Cart;
use App\Models\CartProduct;

use function PHPUnit\Framework\isEmpty;

class CartService
{
    // 價錢總計
    public function calculatePrice(CartProduct $cartProduct)
    {
        $cartProductQuantity = $cartProduct->quantity;
        $cartProductPrice = $cartProduct->product->price;

        $totalPrice = $cartProductQuantity * $cartProductPrice;

        return $totalPrice;
    }

    // 檢查購物車是否屬於該使用者
    public function checkCartOwner(int $cartId)
    {
        $userId = auth()->user()->id;
        $userCartId = Cart::query()->where('user_id', $userId)->value('id');

        if ($cartId !== $userCartId) {
            throw new ForbiddenException('無權限操作此購物車', $userCartId, $cartId);
        }
    }

    // 檢查購物車是否有商品
    public function checkCartProductExist($cartProducts)
    {
        if (isEmpty($cartProducts)) {
            return response()->json([
                'message' => '購物車是空的，請先加入商品',
            ], 400);
        }
    }
}
