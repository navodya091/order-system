<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'total', 'status'];

    /**
     * ----------------------------------------
     * Order Status Constants
     * ----------------------------------------
     */
    public const STATUS_PENDING            = 'pending';
    public const STATUS_RESERVED           = 'reserved';
    public const STATUS_PROCESSING_PAYMENT = 'processing_payment';
    public const STATUS_PAYMENT_FAILED     = 'payment_failed';
    public const STATUS_PAID               = 'paid';
    public const STATUS_FINALIZED          = 'finalized';
    public const STATUS_FAILED             = 'failed';
    /**
     * ----------------------------------------
     * Relationships
     * ----------------------------------------
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function notifications()
    {
        return $this->hasMany(NotificationHistory::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
