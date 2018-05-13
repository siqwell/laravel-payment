<?php

use App\Services\Payment\Contracts\CurrencyContract;
use Illuminate\Support\Carbon;
use Siqwell\Payment\Helpers\Currency;

if (!function_exists('currency')) {
    /**
     * @return Currency
     */
    function currency(): Currency
    {
        return app(Currency::class);
    }
}

if (!function_exists('exchange')) {
    /**
     * @param float            $amount
     * @param CurrencyContract $to
     * @param CurrencyContract $from
     * @param Carbon|null      $date
     *
     * @return float
     * @throws \Siqwell\Payment\Exceptions\ExchangeException
     */
    function exchange(float $amount, CurrencyContract $to, CurrencyContract $from, Carbon $date = null)
    {
        return round(currency()->exchange($amount, $to, $from, $date), 2);
    }
}
