<?php

namespace Siqwell\Payment\Coin;

use Illuminate\Http\Request;
use Omnipay\WebMoney\Message\CompletePurchaseResponse;
use Omnipay\WebMoney\Message\PurchaseResponse;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Contracts\PaymentInterface;
use Siqwell\Payment\Entities\Gateway as GatewayEntity;
use Siqwell\Payment\Exceptions\DriverException;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;
use Siqwell\Payment\Support\Location;

/**
 * Class CoinPayments
 * @package App\Services\Gateway\Drivers
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
        /** @var GatewayEntity $gateway */
        $gateway = $this->gateway;

        /** @var PurchaseResponse $result */
        $result = $this->omnipay->purchase([
            'amount'        => $contract->getAmount(),
            'transactionId' => $contract->getId(),
            'description'   => $contract->getDescription(),
            'notifyUrl'     => $contract->getResultUrl(['payment_id' => $contract->getId()]),
            'currency2'     => $gateway->getParameterByKey('currency2'),
        ])->send();

        return new PurchaseRequest(new Location($result->getRedirectUrl()));
    }

    /**
     * @param Request               $request
     * @param PaymentInterface|null $payment
     *
     * @return CompleteRequest
     * @throws DriverException
     */
    public function complete(Request $request, PaymentInterface $payment = null): CompleteRequest
    {
        if (!$payment_id = $request->get('payment_id')) {
            throw new DriverException('Please specity payment ID');
        }

        /** @var CompletePurchaseResponse $response */
        $response = $this->omnipay->completePurchase([
            'amount' => $payment->getAmount()
        ])->send();

        $reference = $response->getTransactionReference();

        return new CompleteRequest($payment_id, $reference);
    }
}