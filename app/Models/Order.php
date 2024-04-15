<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_data',
        'total',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'product_data' => 'array',
        'total' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
