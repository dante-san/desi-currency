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
        // Basic: @rupee(123456) → ₹1,23,456.78
        Blade::directive('rupee', function ($expression) {
            return "<?php echo app('currency')->format($expression); ?>";
        });

        // Short form: @rs(123456) → ₹1,23,456.78
        Blade::directive('rs', function ($expression) {
            return "<?php echo app('currency')->format($expression); ?>";
        });

        // No symbol: @amount(123456) → 1,23,456.78
        Blade::directive('amount', function ($expression) {
            return "<?php echo app('currency')->format($expression, false); ?>";
        });

        // Lakhs: @lakh(500000) → ₹5.00 Lakhs
        Blade::directive('lakh', function ($expression) {
            return "<?php echo app('currency')->toLakhs($expression); ?>";
        });

        // Crores: @crore(10000000) → ₹1.00 Crores
        Blade::directive('crore', function ($expression) {
            return "<?php echo app('currency')->toCrores($expression); ?>";
        });

        // Short: @short(1500000) → ₹15L
        Blade::directive('short', function ($expression) {
            return "<?php echo app('currency')->toShorthand($expression); ?>";
        });

        // Word: @word(1500000) → ₹15 Lakh
        Blade::directive('word', function ($expression) {
            return "<?php echo app('currency')->toWords($expression); ?>";
        });

        // Spell: @spell(12345.67) → Twelve Thousand Three Hundred...
        Blade::directive('spell', function ($expression) {
            return "<?php echo app('currency')->toIndianWords($expression); ?>";
        });

        // Round: @round(123456.78) → ₹1,23,457
        Blade::directive('round', function ($expression) {
            return "<?php echo app('currency')->formatWhole($expression); ?>";
        });

        // Just symbol: ₹
        Blade::directive('currency', function () {
            return "<?php echo app('currency')->symbol(); ?>";
        });

        // Conditionals
        Blade::if('lakh', function ($amount) {
            return app('currency')->isLakhsRange($amount);
        });

        Blade::if('crore', function ($amount) {
            return app('currency')->isCroresRange($amount);
        });
    }
}
