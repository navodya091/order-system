<?php

namespace App\Console\Commands;

use App\Jobs\ProcessOrder;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Console\Command;

class ImportOrders extends Command
{
    protected $signature = 'orders:import {file}';
    protected $description = 'Import orders and order items from CSV';

    public function handle()
    {
        $file = $this->argument('file');
        if (!file_exists($file)) {
            return $this->error('File not found');
        }

        $rows = array_map('str_getcsv', file($file));
        array_shift($rows); // remove header

        foreach ($rows as $row) {
            [$customerName, $customerEmail, $items] = $row;

            // Create or find customer
            $customer = Customer::firstOrCreate(
                ['email' => $customerEmail],
                ['name' => $customerName]
            );

            $orderTotal = 0;

            // Create order
            $order = $customer->orders()->create([
                'total' => 0, // will update after calculating
                'status' => 'pending'
            ]);

            // $items format: "1:2,3:1" => product_id:quantity
            $items = explode(',', $items);

            foreach ($items as $item) {
                [$productId, $quantity] = explode(':', $item);
                $product = Product::find($productId);
                if (!$product) continue;

                // Calculate total
                $itemTotal = $product->price * (int)$quantity;
                $orderTotal += $itemTotal;

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => (int)$quantity,
                    'price' => $product->price, // optional: store price at time of order
                ]);
            }

            // Update order total
            $order->update(['total' => $orderTotal]);

            // Dispatch workflow
            ProcessOrder::dispatch($order->id);
        }

        $this->info('Orders and order items queued for processing.');
    }
}
