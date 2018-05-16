<?php
namespace Siqwell\Payment\Drivers;

use Illuminate\Http\Request;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;
use Siqwell\Payment\Support\Location;
use Siqwell\Payment\Traits\ExitTrait;

/**
 * Class Dummy
 * @package Siqwell\Payment\Drivers
 */
class Gateway extends BaseDriver
{
    use ExitTrait;
    /**
     * @param PaymentContract $contract
     *
     * @return PurchaseRequest
     */
    public function purchase(PaymentContract $contract): PurchaseRequest
    {
        return new PurchaseRequest(
            new Location($contract->getResultUrl() . '?payment_id=' . $contract->getId())
        );
    }

    /**
     * @param Request $request
     *
     * @return CompleteRequest
     */
    public function complete(Request $request): CompleteRequest
    {
        return new CompleteRequest($request->get('payment_id'), time());
    }
}