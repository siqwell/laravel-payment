<?php
namespace App\Services\Payment\Contracts;

/**
 * Interface CurrencyContract
 * @package App\Services\Payment\Contracts
 */
interface CurrencyContract
{
    /**
     * @return string
     */
    public function getCode(): string;
}