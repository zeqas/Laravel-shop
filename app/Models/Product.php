<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
    ];

    public function carts()
    {
        return $this->belongsToMany(Cart::class);
    }
}
