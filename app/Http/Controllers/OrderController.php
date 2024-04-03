<?php

namespace App\Http\Controllers;

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
     *   "id": 1,
     *   "user_id": 1,
     *   "product_data": [
     *     "product": {
     *        "id": "1",
     *        "price": "100",
     *        "name": "Apple",
     *        "stock": 10
     *     },
     *      "quantity": 1,
     *      "product_id": 1
     *    },
     *    {
     *    ...
     *    }
     *   ],
     *   "total": 100,
     *   "created_at": "2024-04-03T10:02:41.000000Z",
     *   "updated_at": "2024-04-03T10:02:41.000000Z"
     * }
     */
    public function index()
    {
        $userId = auth()->user()->id;
        $orders = Order::query()->where('user_id', $userId)->get();

        return response()->json($orders, 201);
    }
}
