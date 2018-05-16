<?php
namespace Siqwell\Payment\Drivers;

use Illuminate\Http\Request;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Contracts\PaymentInterface;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;
use Siqwell\Payment\Support\Location;

/**
 * Class Dummy
 * @package Siqwell\Payment\Drivers
 */
class Gateway extends BaseDriver
{
    /**
     * @param PaymentContract       $contract
     * @param PaymentInterface|null $payment
     *
     * @return PurchaseRequest
     */
    public function purchase(PaymentContract $contract, PaymentInterface $payment = null): PurchaseRequest
    {
        return new PurchaseRequest(
            new Location($contract->getResultUrl(['payment_id' => $contract->getId()]))
        );
    }

    /**
     * @param Request               $request
     * @param PaymentInterface|null $payment
     *
     * @return CompleteRequest
     */
    public function complete(Request $request, PaymentInterface $payment = null): CompleteRequest
    {
        return new CompleteRequest($request->get('payment_id'), time());
    }
}