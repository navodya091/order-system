<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RefundController;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::get('/dashboard/refresh-kpis', [DashboardController::class, 'refreshKpis']);
Route::post('/refunds', [RefundController::class, 'store']);
