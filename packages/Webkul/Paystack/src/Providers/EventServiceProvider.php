<?php

namespace Webkul\Paystack\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Webkul\Theme\ViewRenderEventManager;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    

        // Event::listen('sales.invoice.save.after', 'Webkul\Paystack\Listeners\Transaction@saveTransaction');
        Event::listen('checkout.order.save.after', 'Webkul\Paystack\Listeners\GenerateInvoice@handle');
    }
}
