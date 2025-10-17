<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = ['order_id','amount','status'];
    
    public const STATUS_REFUNDED           = 'refunded';

    public function order() { return $this->belongsTo(Order::class); }
}

