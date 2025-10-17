<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use App\Jobs\SendOrderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Handle the job execution.
     */
    public function handle(): void
    {
        $order = Order::with('items')->find($this->orderId);

        if (!$order) {
            Log::error("Order not found: {$this->orderId}");
            return;
        }

        try {
            // Step 1: Reserve stock
            $this->reserveStock($order);

            // Step 2: Simulate payment
            $success = $this->simulatePayment($order);

            if (!$success) {
                throw new Exception("Payment failed for order {$order->id}");
            }

            // Step 3: Finalize order
            $order->update(['status' => Order::STATUS_FINALIZED]);
            Log::info("✅ Order {$order->id} finalized successfully.");

            // Step 4: Notify customer
            SendOrderNotification::dispatch($order->id, Order::STATUS_FINALIZED, $order->total);

        } catch (Exception $e) {
            // Rollback stock
            $this->rollbackStock($order);
            $order->update(['status' => Order::STATUS_FAILED]);

            Log::error("❌ Order {$order->id} failed: " . $e->getMessage());
            SendOrderNotification::dispatch($order->id, Order::STATUS_FAILED, $order->total);
        }
    }

    /**
     * Reserve stock for each product in the order.
     */
    private function reserveStock(Order $order): void
    {
        $order->update(['status' => Order::STATUS_RESERVED]);

        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);

            if (!$product || $product->stock < $item->quantity) {
                throw new Exception("Insufficient stock for product ID {$item->product_id}");
            }

            $product->decrement('stock', $item->quantity);
        }

        Log::info("📦 Stock reserved for order {$order->id}");
    }

    /**
     * Simulate a payment process.
     */
    private function simulatePayment(Order $order): bool
    {
        $order->update(['status' => Order::STATUS_PROCESSING_PAYMENT]);
        sleep(1); // simulate a delay

        $success = rand(0, 1) === 1;

        $order->update([
            'status' => $success ? Order::STATUS_PAID : Order::STATUS_PAYMENT_FAILED
        ]);

        Log::info("💳 Payment " . ($success ? 'succeeded' : 'failed') . " for order {$order->id}");

        return $success;
    }

    /**
     * Roll back reserved stock in case of failure.
     */
    private function rollbackStock(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->increment('stock', $item->quantity);
            }
        }

        Log::warning("🔁 Stock rolled back for order {$order->id}");
    }
}
