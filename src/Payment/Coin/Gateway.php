<?php

namespace Siqwell\Payment\Coin;

use Illuminate\Http\Request;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;
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
     * @return array
     */
    public function purchase(PaymentContract $contract): array
    {
        /** @var GatewayEntity $gateway */
        $gateway = $contract->getGateway();

        return [
            'transactionId' => $contract->getId(),
            'description'   => $contract->getDescription(),
            'currency2'     => $gateway->getParameterByKey('currency2'),
            'amount'        => $contract->getAmount(),
            //TODO NotifyUrl????
            'notifyUrl'     => $contract->getUrl('notify'),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function complete(Request $request)
    {
        return [
            'amount' => $request->get('amount')
        ];
    }
}