<?php

namespace Siqwell\Payment\PayPal;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Omnipay\CoinPayments\Message\CompletePurchaseResponse;
use Omnipay\PayPal\Message\ExpressAuthorizeResponse;
use Omnipay\PayPal\Message\ExpressCompletePurchaseResponse;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Contracts\PaymentInterface;
use Siqwell\Payment\Contracts\StatusContract;
use Siqwell\Payment\Entities\Currency;
use Siqwell\Payment\Exceptions\DriverException;
use Siqwell\Payment\Exceptions\ExchangeException;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;
use Siqwell\Payment\Support\Location;

/**
 * Class ExpressGateway
 * @package Siqwell\Payment\PayPal
 */
class ExpressGateway extends BaseDriver
{
    /**
     * @param PaymentContract       $contract
     * @param PaymentInterface|null $payment
     *
     * @return PurchaseRequest
     * @throws DriverException
     * @throws ExchangeException
     */
    public function purchase(PaymentContract $contract, PaymentInterface $payment = null): PurchaseRequest
    {
        //Hot fix for amount conversion
        /** @var Currency $currency */
        $currency = Currency::where('id', $payment->getCurrencyId())->first();

        $converted =
            $this->currency
                ->exchange(
                    $contract->getAmount(),
                    $this->omnipay->getParameter('currency_id'),
                    $currency->code
                );

        /** @var ExpressAuthorizeResponse $result */
        $result = $this->omnipay->purchase([
            'amount'        => $converted,
            'transactionId' => $contract->getId(),
            'description'   => $contract->getDescription(),
            'returnUrl'     => $contract->getReturnUrl(['id' => $contract->getId()]),
            'cancelUrl'     => $contract->getFailedUrl(),
            'currency'      => 'USD',
        ])->send();

        if (!$redirect = $result->getRedirectUrl()) {
            throw new DriverException($result->getMessage());
        }

        return new PurchaseRequest(new Location($redirect));
    }

    /**
     * @param Request               $request
     * @param PaymentInterface|null $payment
     *
     * @return CompleteRequest
     */
    public function complete(Request $request, PaymentInterface $payment = null): CompleteRequest
    {
        /** @var ExpressCompletePurchaseResponse $response */
        $response = $this->omnipay->completePurchase([
            'amount' => $payment->getAmount()
        ])->send();

        if ($response->isSuccessful()) {
            return new CompleteRequest($payment->getInvoiceId(), StatusContract::ACCEPT, $response->getTransactionReference());
        }

        if ($response->isCancelled()) {
            return new CompleteRequest($payment->getInvoiceId(), StatusContract::CANCEL);
        }

        return new CompleteRequest($payment->getInvoiceId(), StatusContract::PROCESS);
    }
}