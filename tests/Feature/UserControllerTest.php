<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 註冊 成功
     */
    public function test_register_success()
    {
        // 發送請求
        $response = $this->postJson("api/register", [
            'name' => 'tester',
            'email' => 'tester@mail.com',
            'password' => 'hashedPassword',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
        ]);

        // 檢查資料庫
        $this->assertDatabaseHas('users', [
            'name' => 'tester',
            'email' => 'tester@mail.com',
        ]);
    }

    /**
     * 登入 成功
     */
    public function test_login_success()
    {
        $userRegisterData = [
            'name' => 'tester',
            'email' => 'tester@mail.com',
            'password' => 'hashedPassword'
        ];

        // 建立用戶
        $this->postJson("api/register", $userRegisterData);

        // 發送請求
        $response = $this->postJson("api/login", [
            'email' => 'tester@mail.com',
            'password' => 'hashedPassword'
        ]);

        // 檢查狀態碼和回傳內容是否有 id 和 token
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'token',
        ]);
    }

    /**
     * 登入 失敗
     */
    public function test_login_fail()
    {
        // 發送請求
        $response = $this->postJson("api/login", [
            'email' => 'test@mail.wrong.com',
            'password' => '123',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }

    /**
     * 取得使用者資訊 成功
     */
    public function test_me_success()
    {
        // 建立用戶
        $user = User::factory()->create();

        $this->actingAs($user)->getJson("api/me")
            ->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    /**
     * 取得使用者資訊 無效 token 失敗
     */
    public function test_me_fail()
    {
        $this->getJson("api/me")
            ->assertStatus(401)
            ->assertJson([
                "success" => false,
                "msg" => "無效Token",
            ]);
    }
}
