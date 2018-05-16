<?php
namespace Siqwell\Payment\Support;

use Siqwell\Payment\Contracts\GatewayContract;
use Siqwell\Payment\Contracts\PaymentContract;

/**
 * Class Payment
 * @package Siqwell\Payment
 */
class Payment implements PaymentContract
{
    /**
     * @var mixed
     */
    protected $payment_id;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var GatewayContract
     */
    protected $gateway;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var string
     */
    protected $currrency;

    /**
     * Payment constructor.
     *
     * @param                 $payment_id
     * @param GatewayContract $gateway
     * @param float           $amount
     * @param array           $attributes
     */
    public function __construct($payment_id, GatewayContract $gateway, float $amount, array $attributes = [])
    {
        $this->payment_id = $payment_id;
        $this->gateway    = $gateway;
        $this->amount     = $amount;
        $this->attributes = $attributes;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->payment_id;
    }

    /**
     * @return GatewayContract
     */
    public function getGateway(): GatewayContract
    {
        return $this->gateway;
    }

    /**
     * @return string
     */
    public function getGatewayName(): string
    {
        return $this->gateway->getName();
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->gateway->getDriver();
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currrency;
    }

    /**
     * @return string
     */
    public function getResultUrl(): string
    {
        return $this->attributes['result_url'];
    }

    /**
     * @return string
     */
    public function getSuccessUrl(): string
    {
        return $this->attributes['success_url'];
    }

    /**
     * @return string
     */
    public function getFailedUrl(): string
    {
        return $this->attributes['failed_url'];
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->attributes['description'];
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function getAttributeByKey(string $key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * @return string
     */
    public function getReturnUrl(): string
    {
        return $this->attributes['return_url'];
    }

    /**
     * @return array
     */
    public function getClient(): array
    {
        return $this->attributes['client'];
    }
}