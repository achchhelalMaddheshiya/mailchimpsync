<?php

use App\Http\Controllers\MailchimpWebhookController;
use Illuminate\Support\Facades\Route;

// use Illuminate\Support\Str;

Route::get('/', function () {
    // dd(Str::random(16));
    return view('welcome');
});
Route::get('/mailchimp/webhook', MailchimpWebhookController::class);
