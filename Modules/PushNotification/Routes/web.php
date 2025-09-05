<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use \Modules\PushNotification\Http\Controllers\PushNotificationController;
use \Illuminate\Support\Facades\Route;

Route::controller(PushNotificationController::class)->group(function () {

    Route::post('/store-fcm-token', 'storeFcmToken');
    Route::post('/subscribe-to-topic', 'subscribeToTopic');

    Route::get('/send-notification', 'sendTestNotification');
    Route::get('/send-topic-notification', 'notification');
});