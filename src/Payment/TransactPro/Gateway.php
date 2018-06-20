<?php

namespace Siqwell\Payment\TransactPro;

use Illuminate\Http\Request;
use Omnipay\TransactPro\Message\CompletePurchaseResponse;
use Omnipay\TransactPro\Message\PurchaseResponse;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Contracts\PaymentInterface;
use Siqwell\Payment\Contracts\StatusContract;
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
     * @param PaymentContract       $contract
     * @param PaymentInterface|null $payment
     *
     * @return PurchaseRequest
     * @throws DriverException
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function purchase(PaymentContract $contract, PaymentInterface $payment = null): PurchaseRequest
    {
        /** @var PurchaseResponse $result */
        $result = $this->omnipay->purchase([
            'transactionId' => md5($contract->getId() . time()),
            'amount'        => $contract->getAmount(),
            'description'   => $contract->getDescription(),
            'notifyUrl'     => $contract->getResultUrl(),
            'returnUrl'     => $contract->getReturnUrl(),
            'cancelUrl'     => $contract->getFailedUrl(),
            'client'        => $contract->getCustomer(),
            'clientIp'      => $contract->getCustomerValue('ip')
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
     * @throws DriverException
     */
    public function complete(Request $request, PaymentInterface $payment = null): CompleteRequest
    {
        if (!$transactionId = $request->post('ID')) {
            throw new DriverException('Please specify transaction ID');
        }

        /** @var CompletePurchaseResponse $response */
        $response = $this->omnipay->completePurchase(['transactionId' => $transactionId])->send();

        if ($response->isSuccessful()) {
            return new CompleteRequest($payment->getInvoiceId(), StatusContract::ACCEPT, $response->getTransactionId(), $response->getTransactionReference());
        }

        return new CompleteRequest($payment->getInvoiceId(), StatusContract::PROCESS);
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
}