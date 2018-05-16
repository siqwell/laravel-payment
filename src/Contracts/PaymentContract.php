<?php
namespace Siqwell\Payment\Contracts;
/**
 * Interface PaymentContract
 * @package Siqwell\Payment\Contracts
 */
interface PaymentContract
{
    /**
     * PaymentContract constructor.
     *
     * @param                 $payment_id
     * @param GatewayContract $gateway
     * @param float           $amount
     * @param array           $attributes
     */
    public function __construct($payment_id, GatewayContract $gateway, float $amount, array $attributes = []);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return GatewayContract
     */
    public function getGateway(): GatewayContract;

    /**
     * @return string
     */
    public function getGatewayName(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return string
     */
    public function getDriver(): string;

    /**
     * @return float
     */
    public function getAmount(): float;

    /**
     * @return array
     */
    public function getCustomer(): array;

    /**
     * @param array $params
     *
     * @return string
     */
    public function getResultUrl(array $params = []): string;

    /**
     * @param array $params
     *
     * @return string
     */
    public function getSuccessUrl(array $params = []): string;

    /**
     * @param array $params
     *
     * @return string
     */
    public function getReturnUrl(array $params = []): string;

    /**
     * @param array $params
     *
     * @return string
     */
    public function getFailedUrl(array $params = []): string;

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getAttributeByKey(string $key);
}