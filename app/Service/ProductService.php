<?php

namespace App\Service;

use App\Models\Product;

class ProductService
{
    /**
     * Search products by keyword and price range.
     *
     * @param string $keyword 關鍵字 Example: Apple
     * @param float $minPrice 最低價格 Example: 1
     * @param float $maxPrice 最高價格 Example: 1000
     * @return array 符合搜尋結果的產品
     */
    public function search(?string $keyword, ?float $minPrice, ?float $maxPrice)
    {
        $query = Product::query();

        if ($keyword) {
            $query->where('name', 'like', "$keyword%");
        }

        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        $matchingProducts = $query->get();

        return $matchingProducts;
    }
}
