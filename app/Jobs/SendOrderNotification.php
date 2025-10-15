<?php

namespace App\Jobs;

use App\Models\NotificationHistory;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendOrderNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public string $status;

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId, string $status)
    {
        $this->orderId = $orderId;
        $this->status = $status;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $order = Order::find($this->orderId);
        if (!$order) return;

        NotificationHistory::create([
            'order_id' => $order->id,
            'status' => $this->status,
            'total' => $order->total
        ]);

        Log::info("Order {$order->id} processed: {$this->status}");
    }
}
