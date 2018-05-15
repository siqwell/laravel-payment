<?php
namespace Siqwell\Payment;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Omnipay\Common\Exception\InvalidResponseException;
use Siqwell\Payment\Contracts\DriverContract;
use Siqwell\Payment\Contracts\PaymentContract;
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
     * @param PaymentContract $contract
     *
     * @return Requests\PurchaseRequest
     * @throws PurchaseException
     */
    public function purchase(PaymentContract $contract): PurchaseRequest
    {
        /** @var DriverContract $driver */
        if (!$driver = $this->factory->create($contract->getGatewayName(), $contract->getDriver())) {
            throw new PurchaseException("Driver {$contract->getDriver()} not found");
        }

        /** @var PurchaseRequest $request */
        $request = $driver->purchase($contract);

        event(new PurchaseStart($contract, $request));

        return $request;
    }

    /**
     * @param Gateway $gateway
     * @param Request $request
     *
     * @return CompleteRequest|void
     * @throws PurchaseException
     */
    public function complete(Gateway $gateway, Request $request): CompleteRequest
    {
        /** @var DriverContract $driver */
        if (!$driver = $this->factory->create($gateway->getName(), $gateway->getDriver())) {
            throw new PurchaseException("Driver {$gateway->getDriver()} not found");
        }

        try {
            /** @var CompleteRequest $complete */
            $complete = $driver->complete($request);
            event(new PurchaseComplete($complete));
        } catch (InvalidResponseException $exception) {
            event(new PurchaseFailed($request, $exception));
            $driver->failed($request, $exception->getMessage());
        }

        $driver->success($request);
    }
}