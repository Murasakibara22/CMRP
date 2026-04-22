<?php

/**
 * config/payment.php — CMRP
 *
 * Configuration du gateway AdjeminPay.
 * Toutes les valeurs viennent du .env.
 */
return [

    /* ── AdjeminPay ── */
    'client_id'       => env('ADJEMINPAY_CLIENT_ID'),
    'client_secret'   => env('ADJEMINPAY_CLIENT_SECRET'),
    'seller_username' => env('ADJEMINPAY_SELLER_USERNAME'),
    'base_url'        => env('ADJEMINPAY_BASE_URL', 'https://api.adjem.in/v3'),

    /* ── URLs callbacks ── */
    'webhook_url'  => env('ADJEMINPAY_WEBHOOK_URL'),   // POST — reçu par AdjeminPay
    'return_url'   => env('ADJEMINPAY_RETURN_URL'),    // GET  — retour après paiement OK
    'cancel_url'   => env('ADJEMINPAY_CANCEL_URL'),    // GET  — retour après annulation

];
