<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * 產生 Product 的十筆假資料
     * @response scenario=success {
     *   Seeder [database/seeders/ProductSeeder.php] created successfully.
     * }
     */
    public function run(): void
    {
        Product::factory()
            ->count(10)
            ->create();
    }
}
