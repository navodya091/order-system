<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Refund;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        if (!$order) {
            Log::error("Refund failed: Order #{$this->orderId} not found.");
            return;
        }

        // Check if refund already processed
        $existingRefund = Refund::where('order_id', $this->orderId)
                                ->where('status', Refund::STATUS_REFUNDED)
                                ->first();

        if ($existingRefund) {
            Log::warning("Refund already processed for Order #{$this->orderId}, Amount: {$this->amount}");
        }

        // Process refund
        $order->update(['status' => 'refunded']);
        Refund::create([
            'order_id' => $order->id,
            'amount' => $this->amount,
            'status' => Refund::STATUS_REFUNDED
        ]);

        SendOrderNotification::dispatch($order->id, 'refunded', $this->amount);
    }


}
