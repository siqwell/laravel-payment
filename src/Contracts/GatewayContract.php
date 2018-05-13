<?php
namespace Siqwell\Payment\Contracts;

/**
 * Interface GatewayContract
 * @package App\Services\Payment\Contracts
 */
interface GatewayContract
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getDriver(): string;
}