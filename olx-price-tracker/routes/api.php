<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;

Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
Route::get('/unsubscribe/{token}', [SubscriptionController::class, 'unsubscribe']);
