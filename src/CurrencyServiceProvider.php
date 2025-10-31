<?php

namespace Laxmidhar\DesiCurrency;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
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
        $this->registerBladeDirectives();
    }

    /**
     * Register Blade directives
     */
    protected function registerBladeDirectives(): void
    {
        // Format Directives - Standard formatting with symbol
        Blade::directive('currency', function ($expression) {
            return "<?php echo app('desi-currency')->format($expression); ?>";
        });

        Blade::directive('currencyWhole', function ($expression) {
            return "<?php echo app('desi-currency')->formatWhole($expression); ?>";
        });

        Blade::directive('currencyPlain', function ($expression) {
            return "<?php echo app('desi-currency')->format($expression, false); ?>";
        });

        Blade::directive('currencyAccounting', function ($expression) {
            return "<?php echo app('desi-currency')->formatAccounting($expression); ?>";
        });

        // Indian Unit Directives - Lakh & Crore
        Blade::directive('inLakhs', function ($expression) {
            return "<?php echo app('desi-currency')->toLakhs($expression); ?>";
        });

        Blade::directive('inCrores', function ($expression) {
            return "<?php echo app('desi-currency')->toCrores($expression); ?>";
        });

        // Shorthand & Words Directives
        Blade::directive('currencyShort', function ($expression) {
            return "<?php echo app('desi-currency')->toShorthand($expression); ?>";
        });

        Blade::directive('currencyWords', function ($expression) {
            return "<?php echo app('desi-currency')->toWords($expression); ?>";
        });

        Blade::directive('currencySpell', function ($expression) {
            return "<?php echo app('desi-currency')->toIndianWords($expression); ?>";
        });

        // Utility Directives
        Blade::directive('rupeeSymbol', function () {
            return "<?php echo app('desi-currency')->symbol(); ?>";
        });

        // Conditional Directives
        Blade::if('inLakhRange', function ($amount) {
            return app('desi-currency')->isLakhsRange($amount);
        });

        Blade::if('inCroreRange', function ($amount) {
            return app('desi-currency')->isCroresRange($amount);
        });
    }
}
