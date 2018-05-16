<?php

namespace Siqwell\Payment\TransactPro;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Omnipay\WebMoney\Message\CompletePurchaseResponse;
use Omnipay\WebMoney\Message\PurchaseResponse;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Exceptions\DriverException;
use Siqwell\Payment\Requests\CheckRequest;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;
use Siqwell\Payment\Support\Location;

/**
 * Class Gateway
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
        /** @var PurchaseResponse $result */
        $result = $this->omnipay->purchase([
            'transactionId' => md5($contract->getId() . time()),
            'amount'        => $contract->getAmount(),
            'description'   => $contract->getDescription(),
            'notifyUrl'     => $this->getNotifyUrl($contract),
            'returnUrl'     => $contract->getReturnUrl(),
            'cancelUrl'     => $contract->getFailedUrl(),
            'client'        => $contract->getClient()
        ])->send();

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

        if (!$transactionId = $request->get('ID')) {
            throw new DriverException('Please specify transaction ID');
        }

        if ($request->get('Status') !== 'Success') {
            throw new DriverException('Transaction is not success');
        }

        /** @var CompletePurchaseResponse $response */
        $response = $this->omnipay->completePurchase(['transactionId' => $transactionId])->send();

        if (!$reference = $response->getTransactionReference()) {
            $reference = [];
        }

        if (is_array($reference)) {
            $reference = array_merge($reference, [
                'transactionId' => $transactionId
            ]);
        }

        return new CompleteRequest($payment_id, $reference);
    }

    /**
     * @param PaymentContract $contract
     * @param array           $reference
     *
     * @return CheckRequest
     * @throws DriverException
     */
    public function check(PaymentContract $contract, $reference = []): CheckRequest
    {
        if (!isset($reference['transactionId']) || !$transactionId = $reference['transactionId']) {
            throw new DriverException('Please specify transactionId');
        }

        /** @var CompletePurchaseResponse $response */
        $response = $this->omnipay->completePurchase([
            'transactionId' => $reference['transactionId']
        ])->send();

        $responseReference = $response->getTransactionReference();

        if ($response->isSuccessful()) {
            $status = CheckRequest::STATUS_COMPLETED;
        } else {
            $status = CheckRequest::STATUS_DECLINED;
        }

        return new CheckRequest($contract->getId(), $status, $responseReference);
    }

    /**
     * @param PaymentContract $contract
     *
     * @return mixed
     */
    private function getNotifyUrl(PaymentContract $contract)
    {
        return app(UrlGenerator::class)
            ->to(
                $contract->getResultUrl(),
                ['payment_id' => $contract->getId()],
                null
            );
    }
}