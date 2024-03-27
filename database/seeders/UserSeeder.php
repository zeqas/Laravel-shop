<?php

namespace Database\Seeders;

use App\Models\Enum\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * 產生 User 的十筆假資料
     * 會先創建各一個消費者與管理者，再隨機生成八個 User
     * @response scenario=success {
     *   Seeder [database/seeders/UserSeeder.php] created successfully.
     * }
     */
    public function run(): void
    {
        // 創建一個消費者
        User::create([
            'name' => 'customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Customer,
        ]);

        // 創建一個管理員
        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
        ]);

        User::factory()
            ->count(8)
            ->create();
    }
}
