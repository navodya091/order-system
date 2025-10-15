<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessRefund;

class RefundController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        ProcessRefund::dispatch($request->order_id, $request->amount);

        return response()->json(['message' => 'Refund queued successfully']);
    }
}
