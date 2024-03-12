<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @response scenario=success status=200 {
     *   "id": 1,
     *   "name": "Apple",
     *   "price": 100
     * }
     * @response scenario=error status=404 {
     *   "message": "Product Not Found"
     * }
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
        ];
    }
}
