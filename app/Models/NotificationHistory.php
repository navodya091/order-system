<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationHistory extends Model
{
    protected $table = 'notifications';
    protected $fillable = ['order_id','status','total'];
    public function order() { return $this->belongsTo(Order::class); }
}

