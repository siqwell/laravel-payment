<?php
namespace Siqwell\Payment;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Exception\RuntimeException;
use Siqwell\Omnipay\Omnipay;
use Siqwell\Payment\Exceptions\DriverException;

/**
 * Class AbstractDriver
 * @package Siqwell\Payment
 */
abstract class AbstractDriver
{
    /**
     * @var string
     */
    protected $driver;

    /**
     * @var string
     */
    protected $gateway;

    /**
     * @var AbstractGateway
     */
    protected $omnipay;

    /**
     * AbstractDriver constructor.
     *
     * @param string $driver
     * @param string $gateway
     *
     * @throws DriverException
     */
    public function __construct(string $driver, string $gateway)
    {
        $this->driver  = $driver;
        $this->gateway = $gateway;

        try {
            $this->omnipay = Omnipay::gateway($this->gateway);
        } catch (RuntimeException $e) {
            throw new DriverException("Gateway '{$this->gateway}' for '{$driver}' driver not configured in omnipay config file");
        }
    }

    /**
     * @return AbstractGateway
     */
    public function omnipay(): AbstractGateway
    {
        return $this->omnipay;
    }
}