<?php

namespace App\Services;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    protected Google_Service_Sheets $service;
    protected string $spreadsheetId;

    public function __construct()
    {
        $this->spreadsheetId = config('services.google.sheet_id');

        $client = new Google_Client();
        $client->setApplicationName('Google Sheets Integration');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(base_path(config('services.google.credentials_path')));
        $client->setAccessType('offline');

        $this->service = new Google_Service_Sheets($client);
    }

    // Append Rows in Shredsheet
    public function appendRow(array $values, string $range = 'Sheet1')
    {
        // dd($values);
        // if (!$this->contactExists($values[0], 'Sheet1')) {
        $body = new Google_Service_Sheets_ValueRange([
            'values' => [$values],
        ]);

        $params = ['valueInputOption' => 'RAW'];

        return $this->service->spreadsheets_values->append(
            $this->spreadsheetId,
            $range,
            $body,
            $params
        );
        // } else {
        //     // Already exist contacts
        //     Log::info(' Contact-already exist:: .' . json_encode($values));
        //     return true;
        // }
    }

    //get spreadsheet values
    public function getSheetValues(string $range = 'Sheet1!A2:E')
    {
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        return $response->getValues();
    }

    // Check data already exist
    public function contactExists(string $email, string $range = 'Sheet1!A2:A')
    {
        $rows = $this->getSheetValues($range);
        Log::info('contactExists :: ', $rows);
        foreach ($rows as $row) {
            if (isset($row[0]) && strtolower($row[0]) === strtolower($email)) {
                return true;
            }
        }
        return false;
    }
}
