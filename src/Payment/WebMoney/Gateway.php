<?php
namespace Siqwell\Payment\WebMoney;

use Illuminate\Http\Request;
use Omnipay\WebMoney\Message\CompletePurchaseResponse;
use Omnipay\WebMoney\Message\PurchaseResponse;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Contracts\PaymentInterface;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;
use Siqwell\Payment\Support\Form;

/**
 * Class WebMoney
 * @package Siqwell\Payment\Drivers
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
        $result = $this->omnipay->purchase(
            $this->getPurchaseAttributes($contract)
        )->send();

        return new PurchaseRequest(
            new Form($result->getRedirectUrl(), $result->getRedirectData(), $result->getRedirectMethod())
        );
    }

    /**
     * @param Request               $request
     * @param PaymentInterface|null $payment
     *
     * @return CompleteRequest
     */
    public function complete(Request $request, PaymentInterface $payment = null): CompleteRequest
    {
        if ($request->input('LMI_PREREQUEST')) {
            $this->exit('YES');
        }

        if (!$request->input('LMI_PAYMENT_NO') && !$request->input('LMI_PAYMENT_AMOUNT')) {
            $this->exit('YES');
        }

        /** @var CompletePurchaseResponse $response */
        $response   = $this->omnipay->completePurchase($request->all())->send();
        $reference  = $response->getTransactionReference();
        $payment_id = $response->getTransactionId();

        return new CompleteRequest($payment_id, $reference);
    }

    /**
     * @param PaymentContract $contract
     *
     * @return array
     */
    private function getPurchaseAttributes(PaymentContract $contract): array
    {
        return [
            'transactionId' => $contract->getId(),
            'amount'        => $contract->getAmount(),
            'description'   => $contract->getDescription(),
            'notifyUrl'     => $contract->getResultUrl(),
            'returnUrl'     => $contract->getSuccessUrl(),
            'cancelUrl'     => $contract->getFailedUrl(),
        ];
    }
}