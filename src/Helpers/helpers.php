<?php

use Illuminate\Support\Carbon;
use Siqwell\Payment\Contracts\CurrencyContract;
use Siqwell\Payment\Exceptions\ExchangeException;
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
     * @param int              $decimal
     *
     * @return float
     * @throws ExchangeException
     */
    function exchange(float $amount, CurrencyContract $to, CurrencyContract $from, Carbon $date = null, $decimal = 2)
    {
        return round(currency()->exchange($amount, $to, $from, $date), $decimal);
    }
}
