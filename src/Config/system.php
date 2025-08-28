<?php

use Webkul\Sales\Models\Order;

return [
    [
        'key'    => 'sales.payment_methods.wompi',
        'name'   => 'wompi::app.admin.configuration.wompi-gateway',
        'info'   => 'wompi::app.admin.configuration.wompi-gateway-info',
        'sort'   => 6,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.title',
                'type'          => 'text',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => true,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.description',
                'type'          => 'textarea',
                'channel_based' => true,
                'locale_based'  => true,
            ], [
                'name'          => 'image',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.logo',
                'type'          => 'image',
                'info'          => 'admin::app.configuration.index.sales.payment-methods.logo-information',
                'channel_based' => false,
                'locale_based'  => false,
                'validation'    => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'          => 'public_key',
                'title'         => 'wompi::app.admin.configuration.public-key',
                'type'          => 'text',
                'info'          => 'wompi::app.admin.configuration.public-key-info',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'private_key',
                'title'         => 'wompi::app.admin.configuration.private-key',
                'type'          => 'password',
                'info'          => 'wompi::app.admin.configuration.private-key-info',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'sandbox_public_key',
                'title'         => 'wompi::app.admin.configuration.sandbox-public-key',
                'type'          => 'text',
                'info'          => 'wompi::app.admin.configuration.sandbox-public-key-info',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'sandbox_private_key',
                'title'         => 'wompi::app.admin.configuration.sandbox-private-key',
                'type'          => 'password',
                'info'          => 'wompi::app.admin.configuration.sandbox-private-key-info',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'sandbox',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.sandbox',
                'type'          => 'boolean',
                'default_value' => true,
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'instructions',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.instructions',
                'type'          => 'textarea',
                'channel_based' => true,
                'locale_based'  => true,
            ], [
                'name'          => 'generate_invoice',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.generate-invoice',
                'type'          => 'boolean',
                'default_value' => false,
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'invoice_status',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.set-invoice-status',
                'depends'       => 'generate_invoice:1',
                'validation'    => 'required_if:generate_invoice,1',
                'type'          => 'select',
                'options'       => [
                    [
                        'title' => 'admin::app.configuration.index.sales.payment-methods.pending',
                        'value' => 'pending',
                    ], [
                        'title' => 'admin::app.configuration.index.sales.payment-methods.paid',
                        'value' => 'paid',
                    ],
                ],
                'info'          => 'admin::app.configuration.index.sales.payment-methods.set-order-status',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'order_status',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.set-order-status',
                'type'          => 'select',
                'options'       => [
                    [
                        'title' => 'admin::app.configuration.index.sales.payment-methods.pending',
                        'value' => Order::STATUS_PENDING,
                    ], [
                        'title' => 'admin::app.configuration.index.sales.payment-methods.pending-payment',
                        'value' => Order::STATUS_PENDING_PAYMENT,
                    ], [
                        'title' => 'admin::app.configuration.index.sales.payment-methods.processing',
                        'value' => Order::STATUS_PROCESSING,
                    ],
                ],
                'info'          => 'admin::app.configuration.index.sales.payment-methods.generate-invoice-applicable',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'active',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.status',
                'type'          => 'boolean',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'    => 'sort',
                'title'   => 'admin::app.configuration.index.sales.payment-methods.sort-order',
                'type'    => 'select',
                'options' => [
                    [
                        'title' => '1',
                        'value' => 1,
                    ], [
                        'title' => '2',
                        'value' => 2,
                    ], [
                        'title' => '3',
                        'value' => 3,
                    ], [
                        'title' => '4',
                        'value' => 4,
                    ], [
                        'title' => '5',
                        'value' => 5,
                    ], [
                        'title' => '6',
                        'value' => 6,
                    ],
                ],
            ],
        ],
    ],
];
