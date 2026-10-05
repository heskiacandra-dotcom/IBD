<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Midtrans Snap & Payment Gateway Integration for
    | Smart RT neighborhood dues and cash payments.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-demo-rt-05-key'),
    'client_key' => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-demo-rt-05-key'),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => true,
    'is_3ds' => true,

    'snap_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',

    'snap_api_endpoint' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/v1/transactions'
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions',
];
