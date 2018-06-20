<?php
namespace Siqwell\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;
use Siqwell\Payment\Contracts\DriverContract;
use Siqwell\Payment\Contracts\GatewayContract;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Contracts\PaymentInterface;
use Siqwell\Payment\Exceptions\DriverException;
use Siqwell\Payment\Exceptions\OperationException;
use Siqwell\Payment\Requests\CheckRequest;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;

/**
 * Class BaseDriver
 * @package Siqwell\Payment
 */
class BaseDriver implements DriverContract
{
    /**
     * @var AbstractGateway
     */
    protected $omnipay;

    /**
     * BaseDriver constructor.
     *
     * @param GatewayContract $gateway
     *
     * @throws DriverException
     */
    public function __construct(GatewayContract $gateway)
    {
        try {
            /** @var GatewayInterface omnipay */
            $this->omnipay = Omnipay::create($gateway->getDriver());
            $this->omnipay->initialize($gateway->getParams());
        } catch (RuntimeException $e) {
            throw new DriverException(sprintf('Gateway %s for %s driver not configured in omnipay config file', $gateway->getName(), $gateway->getDriver()));
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
     * @param PaymentContract       $contract
     * @param PaymentInterface|null $payment
     *
     * @return PurchaseRequest
     * @throws OperationException
     */
    public function purchase(PaymentContract $contract, PaymentInterface $payment = null): PurchaseRequest
    {
        throw new OperationException(sprintf('Method %s is not implemented', __FUNCTION__));
    }

    /**
     * @param Request               $request
     * @param PaymentInterface|null $payment
     *
     * @return CompleteRequest
     * @throws OperationException
     */
    public function complete(Request $request, PaymentInterface $payment = null): CompleteRequest
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
        return new Response('Error', 500);
    }
}