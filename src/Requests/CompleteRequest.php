<?php
namespace Siqwell\Payment\Requests;

/**
 * Class CompleteRequest
 * @package Siqwell\Payment\Requests
 */
class CompleteRequest
{
    /**
     * @var mixed
     */
    private $payment_id;

    /**
     * @var mixed
     */
    private $reference;

    /**
     * @var array
     */
    private $data;

    /**
     * CompleteRequest constructor.
     *
     * @param       $payment_id
     * @param       $reference
     * @param array $data
     */
    public function __construct($payment_id, $reference, array $data = [])
    {
        $this->payment_id = $payment_id;
        $this->reference  = $reference;
        $this->data       = $data;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->payment_id;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return array
     */
    public function getPaymentData(): array
    {
        return $this->data;
    }
}