<?php
namespace Siqwell\Payment;

use Illuminate\Http\Request;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Exception\RuntimeException;
use Siqwell\Omnipay\Omnipay;
use Siqwell\Payment\Contracts\DriverContract;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Exceptions\DriverException;
use Siqwell\Payment\Exceptions\OperationException;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;
use Siqwell\Payment\Traits\ExitTrait;

/**
 * Class AbstractDriver
 * @package Siqwell\Payment
 */
class BaseDriver implements DriverContract
{
    use ExitTrait;

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

    /**
     * @param PaymentContract $contract
     *
     * @return PurchaseRequest|array
     * @throws OperationException
     */
    public function purchase(PaymentContract $contract)
    {
        throw new OperationException(sprintf('Method %s is not implemented', __FUNCTION__));
    }

    /**
     * @param Request $request
     *
     * @return CompleteRequest|array
     * @throws OperationException
     */
    public function complete(Request $request)
    {
        throw new OperationException(sprintf('Method %s is not implemented', __FUNCTION__));
    }

    /**
     * @param PaymentContract $contract
     *
     * @return array
     * @throws OperationException
     */
    public function check(PaymentContract $contract): array
    {
        throw new OperationException(sprintf('Method %s is not implemented', __FUNCTION__));
    }

    /**
     * @param Request $request
     *
     * @return mixed|void
     */
    public function success(Request $request)
    {
        $this->exit('YES');
    }

    /**
     * @param Request     $request
     * @param string|null $message
     *
     * @return mixed|void
     */
    public function failed(Request $request, string $message = null)
    {
        $this->exit($message ? "ERR: {$message}" : 'ERR');
    }
}