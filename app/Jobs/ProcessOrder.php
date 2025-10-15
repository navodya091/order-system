<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId; // define property

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId; // set property
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $order = Order::find($this->orderId);
        if (!$order) return;

        try {
            $order->update(['status' => 'reserved']);
            sleep(1); // simulate payment
            $order->update(['status' => 'paid']);
            $order->update(['status' => 'finalized']);

            SendOrderNotification::dispatch($order->id, 'success');
        } catch (\Exception $e) {
            $order->update(['status' => 'failed']);
            SendOrderNotification::dispatch($order->id, 'failed');
        }
    }
}
