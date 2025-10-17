<?php

namespace Laxmidhar\DesiCurrency\Facades;

use Illuminate\Support\Facades\Facade;

class Currency extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'desi-currency';
    }
}
