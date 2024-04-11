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
     *  @covers App\Http\Controllers\CartController::store
     *  將商品加入購物車 成功
     */
    public function test_store_success()
    {
        // 創建一個用戶和一個商品
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create();

        // 模擬用戶發送一個請求來將商品添加到購物車中
        $response = $this->actingAs($user)->postJson('api/cart', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // 檢查狀態碼和內容
        $response->assertStatus(201);
        $response->assertJson([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // 檢查購物車和購物車商品的數量
        $this->assertEquals(1, Cart::where('user_id', $user->id)->count());
        $this->assertEquals(1, CartProduct::where('cart_id', $user->cart->id)->count());
    }

    /**
     *  @covers App\Http\Controllers\CartController::show
     *  顯示購物車內容
     */
    public function test_show_success()
    {
        // 創建一個用戶和兩個商品
        $user = User::factory()->create(['role' => 'customer']);
        $product1 = Product::factory()->create(['price' => 100]);
        $product2 = Product::factory()->create(['price' => 200]);

        // 模擬用戶發送兩個請求來將商品添加到購物車中
        $this->actingAs($user)->postJson('api/cart', [
            'product_id' => $product1->id,
            'quantity' => 1,
        ]);
        $this->actingAs($user)->postJson('api/cart', [
            'product_id' => $product2->id,
            'quantity' => 2,
        ]);

        // 模擬用戶發送一個請求來查看購物車
        $response = $this->actingAs($user)->getJson('api/cart');

        // 檢查響應的狀態碼和內容
        $response->assertStatus(201);
        $response->assertJson([
            'cartProducts' => [
                [
                    'product_id' => $product1->id,
                    'quantity' => 1,
                    'product' => [
                        'price' => '100',
                        'name' => $product1->name,
                    ],
                ],
                [
                    'product_id' => $product2->id,
                    'quantity' => 2,
                    'product' => [
                        'price' => '200',
                        'name' => $product2->name,
                    ],
                ],
            ],
            'total' => 500,
        ]);
    }

    /**
     *  @covers App\Http\Controllers\CartController::destroy
     *  刪除購物車特定商品
     */
    public function test_destroy_success()
    {
        // 創建一個用戶和兩個產品
        $user = User::factory()->create(['role' => 'customer']);
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        // 模擬用戶發送兩個請求來將產品添加到購物車中
        $this->actingAs($user)->postJson('api/cart', [
            'product_id' => $product1->id,
            'quantity' => 1,
        ]);
        $this->actingAs($user)->postJson('api/cart', [
            'product_id' => $product2->id,
            'quantity' => 1,
        ]);

        // 模擬用戶發送一個請求來刪除一個產品
        $this->actingAs($user)->deleteJson("api/cart/{$product1->id}");

        // 檢查購物車產品的數量
        $this->assertEquals(1, CartProduct::where('cart_id', $user->cart->id)->count());
    }

    /**
     *  @covers App\Http\Controllers\CartController::clear
     *  清空購物車所有商品
     */
    public function test_clear_success()
    {
        // 創建一個用戶和一個產品
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create();

        // 模擬用戶發送一個請求來將產品添加到購物車中
        $this->actingAs($user)->postJson('api/cart', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // 模擬用戶發送一個請求來清空購物車
        $this->actingAs($user)->deleteJson('api/cart/clear');

        // 檢查購物車產品的數量
        $this->assertEquals(0, CartProduct::where('cart_id', $user->cart->id)->count());
    }

    /**
     *  @covers App\Http\Controllers\CartController::checkout
     *  建立訂單 成功
     */
    public function test_checkout_success()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create(['stock' => 10]);

        // 將商品加入購物車
        $this->actingAs($user)->postJson('api/cart', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($user)->postJson('api/cart/checkout');

        $response->assertStatus(201);
        $response->assertJson(['message' => "訂單建立成功"]);
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

        // 將商品加入購物車
        $this->actingAs($user)->postJson('api/cart', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($user)->postJson('api/cart/checkout');

        $response->assertStatus(400);
        $response->assertJson(['message' => '庫存不足']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 4]);
    }
}
