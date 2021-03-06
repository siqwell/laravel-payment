<?php
namespace Siqwell\Payment\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Omnipay\Common\Exception\InvalidResponseException;
use Siqwell\Payment\Exceptions\OperationException;
use Siqwell\Payment\Requests\CheckRequest;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;

/**
 * Interface DriverContract
 * @package Siqwell\Payment\Contracts
 */
interface DriverContract
{
    /**
     * @param PaymentContract       $contract
     * @param PaymentInterface|null $payment
     *
     * @return PurchaseRequest
     */
    public function purchase(PaymentContract $contract, PaymentInterface $payment = null): PurchaseRequest;

    /**
     * @param Request               $request
     * @param PaymentInterface|null $payment
     *
     * @return CompleteRequest
     */
    public function complete(Request $request, PaymentInterface $payment = null): CompleteRequest;

    /**
     * @param PaymentContract $contract
     * @param array           $reference
     *
     * @return CheckRequest
     * @throws OperationException
     */
    public function check(PaymentContract $contract, $reference = []): CheckRequest;

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function success(Request $request): Response;

    /**
     * @param Request $request
     * @param string  $message
     *
     * @return Response
     */
    public function failed(Request $request, string $message = null): Response;

}