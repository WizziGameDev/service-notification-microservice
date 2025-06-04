<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationLogController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/notifications/notifications-member-logs', [NotificationLogController::class, 'logsMember']);
Route::get('/notifications/notifications-mitra-logs', [NotificationLogController::class, 'logsMitra']);
