<?php

namespace App\Service;

use App\Exceptions\ForbiddenException;
use App\Models\Cart;
use App\Models\CartProduct;

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
    public function checkCartOwner(Cart $cart)
    {
        $userId = auth()->user()->id;

        if ($cart->user_id !== $userId) {
            throw new ForbiddenException('無權限操作此購物車');
        }
    }
}
