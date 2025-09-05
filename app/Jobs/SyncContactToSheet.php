<?php

namespace App\Jobs;

use App\Services\GoogleSheetsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncContactToSheet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public array $contact) {}

    public function handle(GoogleSheetsService $sheets)
    {
        if (!$sheets->contactExists($this->contact['email'], 'Sheet1')) {
            $sheets->appendRow([
                $this->contact['email'],
                $this->contact['first_name'],
                $this->contact['last_name'],
                $this->contact['signup_date'],
                $this->contact['tags'],
            ]);
        }
    }
}
