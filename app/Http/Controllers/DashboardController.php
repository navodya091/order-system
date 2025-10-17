<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use App\Models\Order;
use App\Models\Customer;

class DashboardController extends Controller
{
    public function refreshKpis()
    {
        $todayOrders = Order::whereDate('created_at', today())->get();

        Redis::set('daily_revenue', $todayOrders->sum('total'));
        Redis::set('daily_order_count', $todayOrders->count());
        Redis::set('avg_order_value', $todayOrders->avg('total'));

        $topCustomers = Customer::withSum('orders','total')
            ->orderByDesc('orders_sum_total')
            ->limit(5)
            ->get();

        Redis::set('top_customers', $topCustomers->toJson());

        return response()->json([
            'message' => 'KPIs and leaderboard refreshed successfully',
            'daily_revenue' => Redis::get('daily_revenue'),
            'daily_order_count' => Redis::get('daily_order_count'),
            'avg_order_value' => Redis::get('avg_order_value'),
            'top_customers' => json_decode(Redis::get('top_customers'))
        ]);
    }
}
