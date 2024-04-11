<?php

namespace Database\Factories;

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

        // 創建一個包含該商品的 product_data json 資料
        // FIXME 建立符合 Order.product_data 格式的資料
        $product_data = [
            [
                'product_data' => [
                    'name' => $product->name,
                    'price' => $product->price,
                ],
                'quantity' => $this->faker->numberBetween(1, 3),
                'product_id' => $product->id,
            ],
        ];

        // 計算總價
        $total = array_reduce($product_data, function ($carry, $item) {
            return $carry + $item['product_data']['price'] * $item['quantity'];
        }, 0);

        return [
            'user_id' => $user->id,
            'product_data' => $product_data,
            'total' => $total,
        ];
    }
}
