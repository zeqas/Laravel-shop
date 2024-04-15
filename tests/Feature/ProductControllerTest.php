<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     *  @covers App\Http\Controllers\ProductController::index
     *  取得所有商品列表 成功
     */
    public function test_index_success()
    {
        $user = User::factory()->create(['role' => 'customer']);

        // 創建商品
        $product1 = Product::factory()->create([
            'name' => 'Apple',
            'price' => 100,
            'stock' => 10,
        ]);

        $response = $this->actingAs($user)->getJson('api/products?name=Apple&minPrice=50&maxPrice=150');

        // FIXME 為什麼這樣不能這樣寫?
        // $response = $this->actingAs($user)->getJson('api/products', [
        //     'name' => 'Apple',
        //     'minPrice' => 50,
        //     'maxPrice' => 150,
        // ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'name' => $product1->name,
                    'price' => $product1->price,
                ]
            ],
        ]);
    }

    /**
     * @covers App\Http\Controllers\ProductController::index
     * 無法取得特定商品資訊 因為找不到商品
     */
    public function test_index_find_fail()
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)->getJson('api/products?name=Apple&minPrice=50&maxPrice=150');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Product Not Found']);
    }

    /**
     *  @covers App\Http\Controllers\ProductController::store
     *  新增商品 成功
     */
    public function test_store_success()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->postJson('api/products', [
            'name' => 'Apple',
            'price' => 100,
            'stock' => 10,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'name' => 'Apple',
            'price' => 100,
            'stock' => 10,
        ]);
    }

    /**
     *  @covers App\Http\Controllers\ProductController::update
     *  更新商品 成功
     */
    public function test_update_success()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create([
            'name' => 'TestApple',
            'price' => 200,
            'stock' => 20,
        ]);

        $response = $this->actingAs($user)->putJson("api/products/{$product->id}", [
            'name' => 'Apple',
            'price' => 100,
            'stock' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'Apple',
            'price' => 100,
            'stock' => 10,
        ]);
    }

    /**
     *  @covers App\Http\Controllers\ProductController::destroy
     *  刪除商品 成功
     */
    public function test_destroy_success()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->deleteJson("api/products/{$product->id}");

        $response->assertStatus(204);
    }
}
