<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Jobs\SyncContactToSheet;
use Illuminate\Support\Facades\Log;

class SyncMailchimpContacts extends Command
{
    protected $signature = 'sync:mailchimp-contacts';
    protected $description = 'Importing all Mailchimp contacts to Google Sheets';

    public function handle()
    {
        $apiKey = config('services.mailchimp.api_key');
        $server = config('services.mailchimp.server_prefix');
        $listId = config('services.mailchimp.list_id');
        $offset = 0;
        $count = 100;

        do {
            // Get Contacts with paginations
            $response = Http::withBasicAuth('anystring', $apiKey)
                ->get("https://{$server}.api.mailchimp.com/3.0/lists/{$listId}/members", [
                    'offset' => $offset,
                    'count' => $count,
                ]);

            $members = $response->json('members', []);

            foreach ($members as $member) {

                if ($member['status'] !== 'subscribed') continue;
                /*try {*/
                $contact = [
                    'email' => $member['email_address'],
                    'first_name' => $member['merge_fields']['FNAME'] ?? '',
                    'last_name' => $member['merge_fields']['LNAME'] ?? '',
                    'signup_date' => $member['timestamp_opt'] ? date('Y-d-m h:i:s', strtotime($member['timestamp_opt'])) : '',
                    'tags' => implode(',', $this->gettags($member['tags']))
                ];
                Log::info('$member :: ', $contact);
                SyncContactToSheet::dispatch($contact);
                /*} catch (\Throwable $th) {
                    Log::info('failed contact  :: ', $member['email_address']);
                    continue;
                }*/
            }

            $offset += $count;
        } while (count($members) === $count); // <= Check when members have value equal to count.

        $this->info('Mail chimps fetching completed..');
    }
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
