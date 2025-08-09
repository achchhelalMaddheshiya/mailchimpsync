<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SyncContactToSheet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MailchimpWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        // Verify secret for security
        Log::warning("Webhooks response :: ", [$request->all(), $request->ip()]);
        return true;
        if ($request->query('secretkey') !== config('services.mailchimp.webhookkey')) {
            Log::warning("Unauthorized, 401 :: ", $request->all());
            return response('Unauthorized', 401);
        }

        // Mailchimp sends different event types; we want "subscribe"
        try {
            if ($request->type === 'subscribe') {

                $data = $request->get('data');
                $contact = [
                    'email' => $data['email'],
                    'first_name' => $data['merges']['FNAME'] ?? '',
                    'last_name' => $data['merges']['LNAME'] ?? '',
                    'signup_date' => now()->toDateString(),
                    'tags' => implode(',', $data['tags'] ?? []),
                ];

                // dispatch(new SyncContactToSheet($contact));

            }

            return response('OK', 200);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error("Unauthorized, 401 :: " . $th->getMessage());
        }
    }
}
