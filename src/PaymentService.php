<?php
namespace Siqwell\Payment;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Omnipay\Common\Exception\InvalidResponseException;
use Siqwell\Payment\Contracts\DriverContract;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Contracts\PaymentInterface;
use Siqwell\Payment\Contracts\StatusContract;
use Siqwell\Payment\Entities\Gateway;
use Siqwell\Payment\Events\PurchaseComplete;
use Siqwell\Payment\Events\PurchaseFailed;
use Siqwell\Payment\Events\PurchaseStart;
use Siqwell\Payment\Exceptions\PurchaseException;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;

/**
 * Class PaymentService
 * @package Siqwell\Payment
 */
class PaymentService
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var DriverFactory
     */
    private $factory;

    /**
     * PaymentService constructor.
     *
     * @param               $app
     * @param DriverFactory $factory
     */
    public function __construct($app, DriverFactory $factory)
    {
        $this->app     = $app;
        $this->factory = $factory;
    }

    /**
     * @param PaymentContract       $contract
     * @param PaymentInterface|null $payment
     *
     * @return PurchaseRequest
     * @throws PurchaseException
     */
    public function purchase(PaymentContract $contract, PaymentInterface $payment = null): PurchaseRequest
    {
        /** @var DriverContract $driver */
        if (!$driver = $this->factory->create($contract->getGateway())) {
            throw new PurchaseException("Driver {$contract->getDriver()} not found");
        }

        /** @var PurchaseRequest $request */
        $request = $driver->purchase($contract);

        event(new PurchaseStart($contract, $request));

        return $request;
    }

    /**
     * @param Gateway               $gateway
     * @param Request               $request
     * @param PaymentInterface|null $payment
     *
     * @return Response
     * @throws PurchaseException
     */
    public function complete(Gateway $gateway, Request $request, PaymentInterface $payment = null): Response
    {
        /** @var DriverContract $driver */
        if (!$driver = $this->factory->create($gateway)) {
            throw new PurchaseException("Driver {$gateway->getDriver()} not found");
        }

        try {
            /** @var CompleteRequest $complete */
            $complete = $driver->complete($request, $payment);

            if ($complete->getStatus() == StatusContract::ACCEPT) {
                event(new PurchaseComplete($complete));

                return $driver->success($request);
            }
        } catch (InvalidResponseException $exception) {
            event(new PurchaseFailed($request, $exception));

            return $driver->failed($request, $exception->getMessage());
        }
    }
}