<?php
namespace App\Services\Payment\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Class PurchaseFailed
 * @package App\Services\Payment\Events
 */
class PurchaseFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Request|null
     */
    private $request;

    /**
     * @var InvalidResponseException
     */
    private $exception;

    /**
     * PurchaseFailed constructor.
     *
     * @param Request|null                  $request
     * @param InvalidResponseException|null $exception
     */
    public function __construct(Request $request = null, InvalidResponseException $exception = null)
    {
        $this->request   = $request;
        $this->exception = $exception;
    }
}