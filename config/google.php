<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Google Sheets API integration using Service Account.
    |
    */

    'sheet_id' => env('GOOGLE_SHEET_ID', ''),

    'sheet_range' => env('GOOGLE_SHEET_RANGE', 'Sheet1!A1:AN1000'),

    'credentials_path' => env('GOOGLE_CREDENTIALS_PATH', 'storage/app/google-credentials.json'),

    'application_name' => env('APP_NAME', 'Dashboard Investasi PTPN IV'),

    'scopes' => [
        \Google\Service\Sheets::SPREADSHEETS_READONLY,
    ],
];
