<?php
namespace Siqwell\Payment\Contracts;

use Illuminate\Http\Request;
use Omnipay\Common\Exception\InvalidResponseException;
use Siqwell\Payment\Exceptions\OperationException;
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
     * @return PurchaseRequest|array
     */
    public function purchase(PaymentContract $contract);

    /**
     * @param Request $request
     *
     * @return CompleteRequest|array
     * @throws InvalidResponseException
     */
    public function complete(Request $request);

    /**
     * @param PaymentContract $contract
     *
     * @return array
     * @throws OperationException
     */
    public function check(PaymentContract $contract): array;

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