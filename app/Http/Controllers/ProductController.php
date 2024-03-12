<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Service\ProductService;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * 產品列表 包括搜尋功能
     * @bodyParam name string 產品名稱. Example: Apple
     * @bodyParam minPrice float 最低價格. Example: 1
     * @bodyParam maxPrice float 最高價格. Example: 1000
     */
    public function index(Request $request)
    {
        $keyword = $request->input('name');
        $minPrice = $request->input('minPrice');
        $maxPrice = $request->input('maxPrice');

        $products = $this->productService->search($keyword, $minPrice, $maxPrice);

        // 如果沒有找到產品
        if ($products->isEmpty()) {
            return response()->json(['message' => 'Product Not Found'], 404);
        }

        return ProductResource::collection($products);
    }

    /**
     * 新增產品
     * @bodyParam name string 限制100字元
     * @bodyParam price integer 限制1以上
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|integer|min:1',
        ]);

        $product = Product::create($validatedData);

        return response()->json($product, 201);
    }

    /**
     * 產品詳細資訊
     */
    public function show(string $id)
    {
        //
    }

    /**
     * 更新產品資訊
     * @bodyParam name string 限制100字元
     * @bodyParam price integer 限制1以上
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'string|max:100',
            'price' => 'integer|min:1',
        ]);

        $product->update($validatedData);

        return response()->json($product);
    }

    /**
     * 刪除產品
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
}
