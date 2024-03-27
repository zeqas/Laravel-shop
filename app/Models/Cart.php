<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cart_product'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'cart_product' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        // FIXME: 應該使用哪個寫法？差異在哪？
        // return $this->belongsToMany(Product::class, 'cart_products')
        // ->withPivot('quantity')
        // ->withTimestamps();

        return $this->belongsToMany(Product::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
