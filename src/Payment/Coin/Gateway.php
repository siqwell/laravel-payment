<?php

namespace Siqwell\Payment\Coin;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Omnipay\WebMoney\Message\CompletePurchaseResponse;
use Omnipay\WebMoney\Message\PurchaseResponse;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Exceptions\DriverException;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;
use Siqwell\Payment\Support\Location;
use Siqwell\Payment\Entities\Gateway as GatewayEntity;

/**
 * Class CoinPayments
 * @package App\Services\Gateway\Drivers
 */
class Gateway extends BaseDriver
{
    /**
     * @param PaymentContract $contract
     *
     * @return PurchaseRequest
     */
    public function purchase(PaymentContract $contract): PurchaseRequest
    {
        /** @var GatewayEntity $gateway*/
        $gateway = $this->gateway;

        /** @var PurchaseResponse $result */
        $result = $this->omnipay->purchase(
            [
                'transactionId' => $contract->getId(),
                'description'   => $contract->getDescription(),
                'currency2'     => $gateway->getParameterByKey('currency2'),
                'amount'        => $contract->getAmount(),
                'notifyUrl'     => $this->getNotifyUrl($contract)
            ]
        )->send();

        return new PurchaseRequest(new Location($result->getRedirectUrl()));
    }

    /**
     * @param Request $request
     *
     * @return CompleteRequest
     * @throws DriverException
     */
    public function complete(Request $request): CompleteRequest
    {
        if (!$payment_id = $request->get('payment_id')) {
            throw new DriverException('Please specity payment ID');
        }

        /** @var CompletePurchaseResponse $response */
        $response   = $this->omnipay->completePurchase([
            'amount' => 0.00 // TODO: Add invoice amount
        ])->send();

        $reference  = $response->getTransactionReference();

        return new CompleteRequest($payment_id, $reference);
    }

    /**
     * @param PaymentContract $contract
     *
     * @return mixed
     */
    private function getNotifyUrl(PaymentContract $contract)
    {
        return app(UrlGenerator::class)->to(
            $contract->getResultUrl(),
            ['payment_id' => $contract->getId()],
            null
        );
    }
}