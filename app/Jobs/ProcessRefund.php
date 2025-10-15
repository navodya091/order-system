<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Refund;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessRefund implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public float $amount;

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId, float $amount)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $order = Order::find($this->orderId);
        if (!$order || $order->status === 'refunded') return;

        $order->update(['status' => 'refunded']);

        Refund::create([
            'order_id' => $order->id,
            'amount' => $this->amount,
            'status' => 'processed'
        ]);

        SendOrderNotification::dispatch($order->id, 'refunded');
    }
}
