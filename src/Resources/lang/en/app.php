<?php

return [
    'admin' => [
        'configuration' => [
            'wompi-gateway'            => 'Wompi Payment Gateway',
            'wompi-gateway-info'       => 'Accept payments with Wompi - Credit/Debit Cards and Clave (Panama)',
            'public-key'               => 'Public Key',
            'public-key-info'          => 'Your Wompi public key for production environment',
            'private-key'              => 'Private Key',
            'private-key-info'         => 'Your Wompi private key for production environment (keep secure)',
            'sandbox-public-key'       => 'Sandbox Public Key',
            'sandbox-public-key-info'  => 'Your Wompi public key for sandbox/testing environment',
            'sandbox-private-key'      => 'Sandbox Private Key',
            'sandbox-private-key-info' => 'Your Wompi private key for sandbox/testing environment',
        ],
    ],

    'shop' => [
        'payment' => [
            'wompi' => [
                'title'            => 'Wompi Payment Gateway',
                'description'      => 'Pay securely with credit cards, debit cards, or Clave (Panama)',
                'redirect-message' => 'You will be redirected to Wompi secure payment gateway',
                'processing'       => 'Processing your payment...',
                'please-wait'      => 'Please wait while we process your payment',
                'do-not-refresh'   => 'Please do not refresh or close this page',
            ],
        ],

        'messages' => [
            'payment-success'     => 'Your payment was processed successfully!',
            'payment-failed'      => 'Payment failed. Please try again.',
            'payment-cancelled'   => 'Payment was cancelled. You can try again if you wish.',
            'payment-pending'     => 'Your payment is being processed. You will receive confirmation shortly.',
            'invalid-transaction' => 'Invalid transaction. Please try again.',
            'transaction-expired' => 'Transaction has expired. Please create a new order.',
        ],
    ],
];
