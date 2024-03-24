<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    protected static function newFactory(): Factory
    {
        // PS: 可以省略，會自動使用 model 名稱在 `Database\Factories` 尋找 Factory
        return ProductFactory::new ();
    }
}
