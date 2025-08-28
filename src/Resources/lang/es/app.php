<?php

return [
    'admin' => [
        'configuration' => [
            'wompi-gateway'            => 'Pasarela de Pagos Wompi',
            'wompi-gateway-info'       => 'Acepta pagos con Wompi - Tarjetas de Crédito/Débito y Clave (Panamá)',
            'public-key'               => 'Llave Pública',
            'public-key-info'          => 'Tu llave pública de Wompi para el ambiente de producción',
            'private-key'              => 'Llave Privada',
            'private-key-info'         => 'Tu llave privada de Wompi para el ambiente de producción (mantener segura)',
            'sandbox-public-key'       => 'Llave Pública de Pruebas',
            'sandbox-public-key-info'  => 'Tu llave pública de Wompi para el ambiente de pruebas/sandbox',
            'sandbox-private-key'      => 'Llave Privada de Pruebas',
            'sandbox-private-key-info' => 'Tu llave privada de Wompi para el ambiente de pruebas/sandbox',
        ],
    ],

    'shop' => [
        'payment' => [
            'wompi' => [
                'title'            => 'Pasarela de Pagos Wompi',
                'description'      => 'Paga de forma segura con tarjetas de crédito, débito o Clave (Panamá)',
                'redirect-message' => 'Serás redirigido a la pasarela de pagos segura de Wompi',
                'processing'       => 'Procesando tu pago...',
                'please-wait'      => 'Por favor espera mientras procesamos tu pago',
                'do-not-refresh'   => 'Por favor no actualices ni cierres esta página',
            ],
        ],

        'messages' => [
            'payment-success'     => '¡Tu pago fue procesado exitosamente!',
            'payment-failed'      => 'El pago falló. Por favor intenta nuevamente.',
            'payment-cancelled'   => 'El pago fue cancelado. Puedes intentar nuevamente si lo deseas.',
            'payment-pending'     => 'Tu pago está siendo procesado. Recibirás confirmación en breve.',
            'invalid-transaction' => 'Transacción inválida. Por favor intenta nuevamente.',
            'transaction-expired' => 'La transacción ha expirado. Por favor crea una nueva orden.',
        ],
    ],
];
