<?php
namespace Siqwell\Payment;

use Siqwell\Payment\Contracts\DriverContract;
use Siqwell\Payment\Exceptions\RuntimeException;

/**
 * Class DriverFactory
 * @package Siqwell\Payment
 */
class DriverFactory
{
    const GATEWAY = 'Gateway.php';
    /**
     * @param string $gateway
     * @param string $driver
     *
     * @return DriverContract
     */
    public function create(string $gateway, string $driver): DriverContract
    {
        $class = $this->namespace($driver);

        if (!class_exists($class)) {
            throw new RuntimeException(sprintf('Class %s not found', $class));
        }

        return new $class($driver, $gateway);
    }

    /**
     * Resolve a short gateway name to a full namespaced gateway class.
     *
     * Class names beginning with a namespace marker (\) are left intact.
     * Non-namespaced classes are expected to be in the \Omnipay namespace, e.g.:
     *
     *      \Custom\Gateway     => \Custom\Gateway
     *      \Custom_Gateway     => \Custom_Gateway
     *      Stripe              => \Omnipay\Stripe\Gateway
     *      PayPal\Express      => \Omnipay\PayPal\ExpressGateway
     *      PayPal_Express      => \Omnipay\PayPal\ExpressGateway
     *
     * @param  string  $shortName The short gateway name
     * @return string  The fully namespaced gateway class name
     */
    public function namespace($shortName)
    {
        if (0 === strpos($shortName, '\\')) {
            return $shortName;
        }

        // replace underscores with namespace marker, PSR-0 style
        $shortName = str_replace('_', '\\', $shortName);
        if (false === strpos($shortName, '\\')) {
            $shortName .= '\\';
        }

        return '\\Siqwell\\Payment\\'.$shortName.'Gateway';
    }
}