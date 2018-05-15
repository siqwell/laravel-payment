<?php

namespace Siqwell\Payment\TransactPro;

use App\Entities\Invoice;
use App\Exceptions\GatewayException;
use Illuminate\Http\Request;
use Omnipay\Common\Message\ResponseInterface;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;

/**
 * Class Gateway
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
        $client = [];

        if ($data = $gateway->getData()) {
            $client = $data;
        }

        $user = $gateway->getInvoice()->user()->first();
        $client['email'] = $user->getAttribute('email');

        return [
            'orderId'     => md5($gateway->getTransactionId() . time()), // TODO: Hash trait
            'description' => $gateway->getDescription(),
            'amount'      => $gateway->getAmount(),
            'returnUrl'   => $gateway->getUrl('success'),
            'cancelUrl'   => $gateway->getUrl('failed'),
            'notifyUrl'   => $gateway->getUrl('notify'),
            'client'      => $client
        ];
    }

    /**
     * @param Request        $request
     *
     * @return array
     * @throws GatewayException
     */
    public function complete(Request $request): array
    {
        if (!$transactionId = $request->get('ID')) {
            throw new GatewayException('Please specify transaction ID');
        }

        if ($request->get('Status') !== 'Success') {
            throw new GatewayException('Transaction is not success');
        }

        if (!$invoice = $this->getInvoice()) {
            $invoice = Invoice::whereHas('transactions', function($q) use($transactionId) {
                $q->where('driver', $this->gateway)->where('transaction_id', $transactionId);
            })->first();
        }

        if (!$invoice) {
            throw new GatewayException('No invoice found');
        }

        if (!$invoice->transactions()->where('transaction_id', $transactionId)->first()) {
            throw new GatewayException('This transaction is not attached to invoice');
        }

        return [
            'transactionId' => $transactionId
        ];
    }

    /**
     * @param PaymentContract $contract
     *
     * @return array
     * @throws GatewayException
     */
    public function check(PaymentContract $contract): array
    {
        if (!$invoice = $this->getInvoice()) {
            throw new GatewayException('No invoice found');
        }

        if ($transaction = !$invoice->transactions()->first()) {
            throw new GatewayException('Transaction is not attached to invoice');
        }

        return [
            'transactionId' => $transaction->getAttribute('transaction_id')
        ];
    }

    /**
     * @param ResponseInterface $response
     *
     * @return mixed|void
     */
    public function setReference(ResponseInterface $response)
    {
        $invoice = $this->getInvoice();

        if ($response->isSuccessful()) {
            /** @var ResponseInterface $response */
            $invoice->transactions()->create([
                'driver' => $this->gateway,
                'transaction_id' => $response->getTransactionId()
            ]);
        }

        parent::setReference($response);
    }
}