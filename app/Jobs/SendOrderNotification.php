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
    public ?float $amount;

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId, string $status, ?float $amount = null)
    {
        $this->orderId = $orderId;
        $this->status = $status;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::find($this->orderId);

        if (!$order) {
            Log::warning("Order not found for notification: {$this->orderId}");
            return;
        }

        // Prepare notification data
        $data = [
            'order_id'    => $order->id,
            'customer_id' => $order->customer_id,
            'status'      => $this->status,
            'amount'       => $order->total,
        ];

        // Include refund amount if provided
        if ($this->amount !== null) {
            $data['amount'] = $this->amount;
        }

        // Store notification in DB (history table)
        NotificationHistory::create($data);

        // Structured log for easy tracking
        Log::info('Order notification sent', $data);
    }
}
