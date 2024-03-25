<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * 註冊
     * @bodyParam name string required 名稱 Example: tester
     * @bodyParam email string required 信箱 Example: tester@mail.com
     * @bodyParam password string required 密碼 Example: hashedPassword
     *
     * @response scenario=success status=200 {
     *   "id": 1
     * }
     */
    public function register(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return response()->json([
            'id' => $user->id,
        ]);
    }

    /**
     * 登入
     * @bodyParam email string required 信箱 Example: tester@mail.com
     * @bodyParam password string required 密碼 Example: hashedPassword
     *
     * @response scenario=success status=200 {
     *   "id": 1,
     *   "token": "YOUR_TOKEN"
     * }
     *
     * @response scenario=error status=401 {
     *   "message": "Invalid credentials"
     * }
     */
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            $user = auth('sanctum')->user();
            $token = $user->createToken('MyAPIToken')->plainTextToken;

            return response()->json([
                'id' => $user->id,
                'token' => $token,
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }


    /**
     * 取得目前使用者資訊
     * @response scenario=success status=200 {
     *   "id": 1,
     *   "email": "tester@mail.com"
     * }
     *
     * @response scenario=error status=401 {
     *   "success": "false"
     *   "message": "無效Token"
     * }
     */
    public function me(Request $request)
    {
        $user = auth('sanctum')->user();

        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }
}
