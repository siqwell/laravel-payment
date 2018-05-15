<?php

namespace App\Services\Gateway;

use App\Entities\Invoice;
use App\Exceptions\GatewayException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Omnipay\Common\Message\ResponseInterface;
use Siqwell\Omnipay\Omnipay;

/**
 * Class GatewayService
 * @package App\Services\Gateway
 */
class GatewayService
{
    /**
     * @var Invoice|null
     */
    private $invoice;

    /**
     * @var string
     */
    private $gateway;

    /**
     * @param string $gateway
     */
    public function setGateway(string $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return string
     */
    protected function getGatewayName(): string
    {
        return config('omnipay.gateways.' . $this->gateway . '.gateway');
    }

    /**
     * @param string $parameter
     *
     * @return string
     */
    public function getGatewayParameter(string $parameter): string
    {
        return config('omnipay.gateways.' . $this->gateway . '.parameters.' . $parameter);
    }

    /**
     * @param Invoice $invoice
     *
     * @return $this
     */
    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->setGateway($invoice->getAttribute('gateway'));

        return $this;
    }

    /**
     * @return Invoice|null
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        $amount = currency()->exchange(
            $this->getRealAmount(),
            $this->getGatewayParameter('currency'),
            config('omnipay.currency')
        );

        if ($vat = $this->invoice->getAttribute('vat')) {
            $amount = $amount + $amount * $vat;
        }

        if ($discount = $this->invoice->getAttribute('discount')) {
            $amount = $amount - ($amount * $discount / 100);
        }

        return round($amount, 2);
    }

    /**
     * @return float
     */
    public function getRealAmount(): float
    {
        return $this->invoice->plan->getAttribute('price');
    }

    /**
     * @return int
     */
    public function getTransactionId(): int
    {
        return $this->invoice->getKey();
    }

    /**
     * @return array|null
     */
    public function getData()
    {
        $data = $this->invoice->getAttribute('data');
        return $data ? json_decode($data, true) : null;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return trans('gateway.invoice.description', ['id' => $this->invoice->getKey()]);
    }

    /**
     * @param string $type
     * @param array  $arguments
     *
     * @return string
     */
    public function getUrl(string $type = 'notify', array $arguments = []): string
    {
        // success || failed || notify || pay
        return route('api.invoice.' . $type, array_merge(['id' => $this->getTransactionId()], $arguments));
    }

    /**
     * @param string|null $gateway
     * @param array       $arguments
     *
     * @return string
     */
    public function getNotify(string $gateway = null, array $arguments = []): string
    {
        return route('api.invoice.driver', array_merge(['driver' => $gateway ?: $this->gateway], $arguments));
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function purchase(): Response
    {
        $driver = Omnipay::gateway($this->gateway);

        $gatewayDriver = $this->getDriver();
        $gatewayDriver->setInvoice($this->getInvoice());

        $response = $driver->purchase(
            $gatewayDriver->purchase($this)
        )->send();

        $gatewayDriver->setReference($response);

        if ($response->isRedirect()) {
            return $response->redirect();
        } else {
            throw new GatewayException($response->getMessage());
        }
    }

    /**
     * @param Request $request
     *
     * @return bool|\Omnipay\Common\Message\ResponseInterface
     */
    public function notify(Request $request)
    {
        $driver = Omnipay::gateway($this->gateway);

        $gatewayDriver = $this->getDriver();

        if ($invoice = $this->getInvoice()) {
            $gatewayDriver->setInvoice($invoice);
        }

        $response = $driver->completePurchase(
            $gatewayDriver->complete($this, $request)
        )->send();

        if ($response->isSuccessful()) {
            // TODO: Invoice is null always
            $this->getInvoice()->approve();
            if ($this->supportsConfirm($response)) {
                $this->response($response, 'confirm');
            }
            return $response;
        }

        if ($this->supportsError($response)) {
            $this->response($response, 'error');
        }
        return false;
    }

    /**
     * @return bool
     * @throws GatewayException
     */
    public function check()
    {
        $driver = Omnipay::gateway($this->gateway);

        $gatewayDriver = $this->getDriver();

        if ($invoice = $this->getInvoice()) {
            $gatewayDriver->setInvoice($invoice);
        }

        if (!$this->supportsCheck($gatewayDriver)) {
            throw new GatewayException('Gateway does not support transaction checking');
        }

        $response = $driver->completePurchase(
            $gatewayDriver->check($this)
        )->send();

        if ($response->isSuccessful()) {
            return true;
        }

        return false;
    }

    /**
     * @param ResponseInterface $response
     * @param                   $method
     *
     * @return mixed
     */
    public function response(ResponseInterface $response, $method)
    {
        return call_user_func([$response, $method]);
    }
}
