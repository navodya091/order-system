<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationHistory extends Model
{
    protected $table = 'notification_histories';
    protected $fillable = ['customer_id','order_id','status','amount'];
    public function order() { return $this->belongsTo(Order::class); }
}

