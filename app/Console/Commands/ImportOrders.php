<?php

namespace App\Console\Commands;

use App\Jobs\ProcessOrder;
use App\Models\Customer;
use Illuminate\Console\Command;

class ImportOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:import {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        if (!file_exists($file)) return $this->error('File not found');

        $rows = array_map('str_getcsv', file($file));
        array_shift($rows); // remove header

        foreach ($rows as $row) {
            $customer = Customer::firstOrCreate(['email' => $row[1]], ['name' => $row[0]]);
            $order = $customer->orders()->create(['total' => $row[2], 'status' => 'pending']);

            // Dispatch workflow
            ProcessOrder::dispatch($order->id);
        }

        $this->info('Orders queued for processing');
    }
}
