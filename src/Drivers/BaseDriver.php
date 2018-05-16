<?php
namespace Siqwell\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Exception\RuntimeException;
use Siqwell\Omnipay\Omnipay;
use Siqwell\Payment\Contracts\DriverContract;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Entities\Gateway;
use Siqwell\Payment\Exceptions\DriverException;
use Siqwell\Payment\Exceptions\OperationException;
use Siqwell\Payment\Requests\CheckRequest;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;
use Siqwell\Payment\Traits\ExitTrait;

/**
 * Class BaseDriver
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
     * @var Gateway
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
        $this->gateway = Gateway::where('key', $gateway)->firstOrFail();

        try {
            $this->omnipay = Omnipay::gateway($gateway);
        } catch (RuntimeException $e) {
            throw new DriverException("Gateway '{$gateway}' for '{$driver}' driver not configured in omnipay config file");
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
     * @return PurchaseRequest
     * @throws OperationException
     */
    public function purchase(PaymentContract $contract): PurchaseRequest
    {
        throw new OperationException(sprintf('Method %s is not implemented', __FUNCTION__));
    }

    /**
     * @param Request $request
     *
     * @return CompleteRequest
     * @throws OperationException
     */
    public function complete(Request $request): CompleteRequest
    {
        throw new OperationException(sprintf('Method %s is not implemented', __FUNCTION__));
    }

    /**
     * @param PaymentContract $contract
     * @param array           $reference
     *
     * @return CheckRequest
     * @throws OperationException
     */
    public function check(PaymentContract $contract, $reference = []): CheckRequest
    {
        throw new OperationException(sprintf('Method %s is not implemented', __FUNCTION__));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function success(Request $request): Response
    {
        return new Response('Success', 200);
    }
    /**
     * @param Request $request
     * @param string  $message
     *
     * @return Response
     */
    public function failed(Request $request, string $message = null): Response
    {
        return new Response('Error', 402);
    }
}