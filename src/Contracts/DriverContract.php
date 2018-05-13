<?php
namespace Siqwell\Payment\Contracts;

use Illuminate\Http\Request;
use Omnipay\Common\Exception\InvalidResponseException;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;

/**
 * Interface DriverContract
 * @package Siqwell\Payment\Contracts
 */
interface DriverContract
{
    /**
     * @param PaymentContract $contract
     *
     * @return PurchaseRequest
     */
    public function purchase(PaymentContract $contract): PurchaseRequest;

    /**
     * @param PaymentContract $contract
     *
     * @return array
     */
    public function getPurchaseAttributes(PaymentContract $contract): array;

    /**
     * @param Request $request
     *
     * @return CompleteRequest
     * @throws InvalidResponseException
     */
    public function complete(Request $request): CompleteRequest;

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function success(Request $request);

    /**
     * @param Request $request
     * @param string  $message
     *
     * @return mixed
     */
    public function failed(Request $request, string $message = null);

}