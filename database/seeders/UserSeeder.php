<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * 產生 User 的十筆假資料
     * @response scenario=success {
     *   Seeder [database/seeders/UserSeeder.php] created successfully.
     * }
     */
    public function run(): void
    {
        User::factory()
            ->count(10)
            ->create();
    }
}
