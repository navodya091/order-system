<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id','total','status'];
    public function customer() { return $this->belongsTo(Customer::class); }
    public function notifications() { return $this->hasMany(NotificationHistory::class); }
    public function refunds() { return $this->hasMany(Refund::class); }
}