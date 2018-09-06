<?php

namespace Siqwell\Payment\WebMoney;

use Illuminate\Http\Request;
use Omnipay\CoinPayments\Message\CompletePurchaseResponse;
use Omnipay\CoinPayments\Message\PurchaseResponse;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Contracts\PaymentInterface;
use Siqwell\Payment\Contracts\StatusContract;
use Siqwell\Payment\Exceptions\DriverException;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;
use Siqwell\Payment\Support\Form;

/**
 * Class Gateway
 * @package Siqwell\Payment\CoinPayments
 */
class Gateway extends BaseDriver
{
    /**
     * @param PaymentContract       $contract
     * @param PaymentInterface|null $payment
     *
     * @return PurchaseRequest
     * @throws DriverException
     */
    public function purchase(PaymentContract $contract, PaymentInterface $payment = null): PurchaseRequest
    {
        /** @var PurchaseResponse $result */
        $result = $this->omnipay->purchase([
            'transactionId' => $contract->getId(),
            'description'   => $contract->getDescription(),
            'amount'        => $contract->getAmount(),
            'returnUrl'     => $contract->getReturnUrl(),
            'cancelUrl'     => $contract->getFailedUrl(),
            'notifyUrl'     => $contract->getResultUrl(),
        ])->send();

        return new PurchaseRequest(new Form($result->getRedirectUrl(), $result->getRedirectData()));
    }

    /**
     * @param Request               $request
     * @param PaymentInterface|null $payment
     *
     * @return CompleteRequest
     */
    public function complete(Request $request, PaymentInterface $payment = null): CompleteRequest
    {
        /** @var CompletePurchaseResponse $response */
        $response = $this->omnipay->completePurchase($request->all())->send();

        if ($response->isSuccessful()) {
            return new CompleteRequest($response->getInvoiceId(), StatusContract::ACCEPT, $response->getTransactionReference());
        }

        if ($response->isCancelled()) {
            return new CompleteRequest($payment->getInvoiceId(), StatusContract::CANCEL);
        }

        return new CompleteRequest($payment->getInvoiceId(), StatusContract::PROCESS);
    }
}