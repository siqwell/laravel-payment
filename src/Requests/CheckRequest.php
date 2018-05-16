<?php
namespace Siqwell\Payment\Requests;

/**
 * Class CheckRequest
 * @package Siqwell\Payment\Requests
 */
class CheckRequest
{
    const
        STATUS_CREATED = 0,
        STATUS_PROCESSING = 1,
        STATUS_COMPLETED  = 2,
        STATUS_DECLINED   = 3,
        STATUS_NOT_FOUND  = 4
    ;

    /** @var mixed */
    private $payment_id;
    /** @var mixed */
    private $status;
    /** @var mixed */
    private $reference;

    /**
     * CheckRequest constructor.
     *
     * @param      $payment_id
     * @param      $status
     * @param null $reference
     */
    public function __construct($payment_id, $status = 0, $reference = null)
    {
        $this->payment_id = $payment_id;
        $this->status     = $status;
        $this->reference  = $reference;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->payment_id;
    }

    /**
     * @return bool
     */
    public function isCreated()
    {
        return $this->status === self::STATUS_CREATED;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * @return bool
     */
    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * @return bool
     */
    public function isDeclined()
    {
        return $this->status === self::STATUS_DECLINED;
    }

    /**
     * @return bool
     */
    public function isNotFound()
    {
        return $this->status === self::STATUS_NOT_FOUND;
    }

    /**
     * @return mixed
     */
    public function getReference(): array
    {
        return $this->reference;
    }
}