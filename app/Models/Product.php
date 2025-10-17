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

    /**
     * Get the order items that include this product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
