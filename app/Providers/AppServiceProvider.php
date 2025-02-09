<?php

namespace App\Providers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Observers\InvoiceObserver;
use App\Observers\PaymentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Payment::observe(PaymentObserver::class);
        Invoice::observe(InvoiceObserver::class);
    }
}
