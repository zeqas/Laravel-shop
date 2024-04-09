<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     *  @covers App\Http\Controllers\CartController::checkout
     *  結帳 成功
     */
    public function test_checkout_success()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::create(['user_id' => $user->id]);

        $cartProduct = CartProduct::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $expectedTotal = $cartProduct->quantity * $product->price;

        $response = $this->actingAs($user)->postJson('api/cart/checkout');

        $response->assertStatus(201);
        $response->assertJson(['message' => "結帳成功，總共 $$expectedTotal 元，訂單建立成功"]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    }

    /**
     *  @covers App\Http\Controllers\CartController::checkout
     *  庫存不足
     */
    public function test_checkout_insufficient_stock()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['stock' => 4]);
        $cart = Cart::create(['user_id' => $user->id]);

        CartProduct::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($user)->postJson('api/cart/checkout');

        $response->assertStatus(400);
        $response->assertJson(['message' => '庫存不足']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 4]);
    }
}
