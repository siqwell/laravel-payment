<?php
namespace src\Contracts;

interface PaymentInterface
{
    /**
     * @return int
     */
    public function getInvoiceId(): int;

    /**
     * @return float
     */
    public function getAmount(): float;

    /**
     * @return int
     */
    public function getCurrencyId(): int;
}