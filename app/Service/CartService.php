<?php

namespace App\Service;

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
}
