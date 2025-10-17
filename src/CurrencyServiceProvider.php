<?php

namespace Laxmidhar\DesiCurrency;

use Illuminate\Support\ServiceProvider;
use Laxmidhar\DesiCurrency\Support\CurrencyService;

class CurrencyServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register HelperService as singleton
        $this->app->singleton(CurrencyService::class, function () {
            return new CurrencyService();
        });

        // Register under 'desi-currency' key
        $this->app->singleton('desi-currency', CurrencyService::class);
    }

    public function boot()
    {
        // /
    }
}
