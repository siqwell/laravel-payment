<?php

namespace Siqwell\Payment\CoinPayments;

use Illuminate\Http\Request;
use Omnipay\CoinPayments\Message\CompletePurchaseResponse;
use Omnipay\CoinPayments\Message\PurchaseResponse;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Contracts\PaymentInterface;
use Siqwell\Payment\Exceptions\DriverException;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;
use Siqwell\Payment\Support\Location;

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
     */
    public function purchase(PaymentContract $contract, PaymentInterface $payment = null): PurchaseRequest
    {
        /** @var PurchaseResponse $result */
        $result = $this->omnipay->purchase([
            'amount'        => $contract->getAmount(),
            'transactionId' => $contract->getId(),
            'description'   => $contract->getDescription(),
            'notifyUrl'     => $contract->getResultUrl(['payment_id' => $contract->getId()])
        ])->send();

        return new PurchaseRequest(new Location($result->getRedirectUrl()));
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
        $response = $this->omnipay->completePurchase([
            'amount' => $payment->getAmount()
        ])->send();

        $reference = $response->getTransactionReference();

        return new CompleteRequest($payment->getInvoiceId(), $reference);
    }
}