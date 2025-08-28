<?php

namespace Webkul\Wompi\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Listen to order creation events
        Event::listen('checkout.order.save.after', 'Webkul\Wompi\Listeners\Order@updateTransactionOrderId');

        // Listen to payment events
        Event::listen('sales.order.payment.save.after', 'Webkul\Wompi\Listeners\Order@processPayment');

        // Listen to invoice events for transaction completion
        Event::listen('sales.invoice.save.after', 'Webkul\Wompi\Listeners\Transaction@completeTransaction');

        // Listen to order cancellation events
        Event::listen('sales.order.cancel.after', 'Webkul\Wompi\Listeners\Order@cancelTransaction');
    }
}
