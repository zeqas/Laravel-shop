<?php

namespace Database\Factories;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition()
    {
        // 創建一個用戶和一個商品
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create();

        // 將商品加入購物車
        $cart = Cart::where('user_id', $user->id)->first();
        $cartProduct = CartProduct::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $productData = new CartResource($cartProduct);

        return [
            'user_id' => $user->id,
            'product_data' => $productData,
            'total' => $product->price,
        ];
    }
}
