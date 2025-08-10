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
        try {
            // Verify secret for security
            if ($request->query('secretkey') !== config('services.mailchimp.webhookkey')) {
                Log::warning("Unauthorized webhook access", ['request' => $request->all()]);
                return response('OK', 200); // Always return 200 to prevent Mailchimp from disabling
            }
            // Mailchimp sends different event types; we want "subscribe"
            if ($request->input('type') === 'subscribe') {
                $data = $request->input('data', []);
                $contact = [
                    'email' => $data['email'],
                    'first_name' => $data['merges']['FNAME'] ?? '',
                    'last_name' => $data['merges']['LNAME'] ?? '',
                    'signup_date' => now()->toDateString(),
                    'tags' => implode(',', $this->gettags($data['tags'])) //implode(',', $data['tags'] ?? []),
                ];
                SyncContactToSheet::dispatch($contact);
                Log::info("webhooks log email :: " . $data['email']);
            } else {
                Log::info("webhooks log data => ", $request->all());
            }

            return response('OK', 200);
        } catch (\Throwable $e) {
            Log::error("Webhook error: " . $e->getMessage());
        }
    }

    // Tags with comma seprated
    private function gettags($tags)
    {
        $tagsName = [];
        if (!empty($tags)) {
            foreach ($tags as $value) {
                $tagsName[] = $value['name'];
            }
        }
        return $tagsName;
    }
}
