<?php
namespace Siqwell\Payment\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Requests\PurchaseRequest;

/**
 * Class PurchaseStart
 * @package App\Services\Payment\Events
 */
class PurchaseStart
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PaymentContract
     */
    public $payment;

    /**
     * @var Request|null
     */
    public $request;

    /**
     * @var null
     */
    public $reference;

    /**
     * PurchaseStart constructor.
     *
     * @param PaymentContract      $payment
     * @param PurchaseRequest|null $request
     */
    public function __construct(PaymentContract $payment, PurchaseRequest $request = null)
    {
        $this->payment = $payment;
        $this->request = $request;
    }
}