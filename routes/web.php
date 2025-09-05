<?php

use App\Http\Controllers\MailchimpWebhookController;
use Illuminate\Support\Facades\Route;

// use Illuminate\Support\Str;

Route::get('/', function () {
    // dd(Str::random(16));
    return view('welcome');
});
Route::post('/mailchimp/webhook', [MailchimpWebhookController::class, 'handle']);
// Route::match(['get', 'post'], '/mailchimp/webhook', [MailchimpWebhookController::class,'handle']);
