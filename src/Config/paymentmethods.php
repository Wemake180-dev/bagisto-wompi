<?php

return [
    'wompi' => [
        'code'                      => 'wompi',
        'title'                     => 'Wompi Payment Gateway',
        'description'               => 'Pay securely with Wompi - Credit/Debit Cards and Clave (Panama)',
        'class'                     => 'Webkul\Wompi\Payment\Wompi',
        'sandbox'                   => true,
        'active'                    => false,
        'sort'                      => 6,
        'public_key'                => '',
        'private_key'               => '',
        'sandbox_public_key'        => '',
        'sandbox_private_key'       => '',
        'instructions'              => 'You will be redirected to Wompi secure payment gateway to complete your transaction.',
    ],
];
