<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * @group 訂單 management
 *
 * 訂單管理，顯示狀態
 */
class OrderController extends Controller
{
    /**
     * 訂單列表
     * @response scenario=success status=201 {
     *   "user_id": 1,
     *   "product_data": [
     *     "product": {
     *        "price": "100",
     *        "name": "Apple",
     *     },
     *      "quantity": 1,
     *      "product_id": 1
     *    },
     *    {
     *    ...
     *    }
     *   ],
     *   "total": 100,
     * }
     */
    public function index()
    {
        $userId = auth()->user()->id;
        $orders = Order::query()->where('user_id', $userId)->get();

        // 列出所有訂單
        return response()->json([
            "orders" => OrderResource::collection($orders),
        ], 201);
    }
}
