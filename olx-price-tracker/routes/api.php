<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionController;

Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
Route::delete('/unsubscribe/{token}', [SubscriptionController::class, 'unsubscribe']);
