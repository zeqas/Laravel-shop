<?php

namespace Tests\Feature;

use App\Http\Resources\CartResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    /**
     * 訂單列表
     */
    public function test_index(): void
    {
        // 創建一個用戶和兩個訂單
        $user = User::factory()->create(["role" => "customer"]);
        $order1 = Order::factory()->for($user)->create();
        $order2 = Order::factory()->for($user)->create();

        // 模擬用戶發送一個請求來獲取所有訂單
        $response = $this->actingAs($user)->getJson('api/orders');

        // 檢查響應的狀態碼和內容
        $response->assertStatus(201);
        // FIXME 如何正確轉換 Order 的格式
        $response->assertJson([
            "orders" => [
                [
                    'user_id' => $user->id,
                    'product_data' => $order1->product_data,
                    'total' => $order1->total,
                ],
                [
                    'user_id' => $user->id,
                    'product_data' => $order2->product_data,
                    'total' => $order2->total,
                ],
            ],
        ]);
    }
}
