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
     * \Custom\Gateway     => \Custom\Gateway
     * \Custom_Gateway     => \Custom_Gateway
     * Stripe              => \Siqwell\Payment\Drivers\Stripe
     * PayPal\Express      => \Siqwell\Payment\Drivers\PayPal\Express
     * PayPal_Express      => \Siqwell\Payment\Drivers\Express
     *
     * @param  string $shortName The short gateway name
     *
     * @return string The fully namespaced gateway class name
     */
    protected function namespace($shortName)
    {
        if (0 === strpos($shortName, '\\')) {
            return $shortName;
        }

        // replace underscores with namespace marker, PSR-0 style
        $shortName = str_replace('_', '\\', $shortName);

        return __NAMESPACE__ . sprintf('\\%s\\', $shortName) . self::GATEWAY;
    }
}