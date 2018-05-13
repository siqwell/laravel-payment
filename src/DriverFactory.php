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
    /**
     * Internal storage for all available gateways
     *
     * @var array
     */
    private $gateways = [];

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->gateways;
    }

    /**
     * @param string $gateway
     * @param string $driver
     *
     * @return DriverContract
     */
    public function create(string $gateway, string $driver): DriverContract
    {
        if (isset($this->gateways[$gateway])) {
            $class = $this->gateways[$gateway];
        } else {
            $class = $this->namespace($driver);

            if (!class_exists($class)) {
                throw new RuntimeException("Class '$class' not found");
            }

            $this->gateways[$gateway] = $class;
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
     * @param  string $short_name The short gateway name
     *
     * @return string The fully namespaced gateway class name
     */
    protected function namespace($short_name)
    {
        if (0 === strpos($short_name, '\\')) {
            return $short_name;
        }

        // replace underscores with namespace marker, PSR-0 style
        $short_name = str_replace('_', '\\', $short_name);

        return __NAMESPACE__ . '\\Drivers\\' . $short_name;
    }
}