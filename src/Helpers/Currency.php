<?php
namespace Siqwell\Payment\Helpers;

use Illuminate\Support\Carbon;
use Siqwell\Payment\Contracts\CurrencyContract;
use Siqwell\Payment\Entities\Course;
use Siqwell\Payment\Exceptions\ExchangeException;

/**
 * Class Currency
 * @package Siqwell\Payment\Helpers
 */
class Currency
{
    /**
     * @param float            $amount
     * @param CurrencyContract $to
     * @param CurrencyContract $from
     * @param Carbon|null      $date
     *
     * @return float
     * @throws ExchangeException
     */
    public function exchange(float $amount, CurrencyContract $to, CurrencyContract $from, Carbon $date = null): float
    {
        if ($from->getKey() === $to->getKey()) {
            return $amount;
        }

        /** @var Course $rate */
        if (!$rate = $from->course()->actual($to, $date)->first()) {
            throw new ExchangeException("Error exchange {$from->getCode()} to {$to->getCode()}");
        }

        return $amount * $rate->getAttribute('value');
    }
}
