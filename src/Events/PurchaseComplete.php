<?php
namespace Siqwell\Payment\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Siqwell\Payment\Requests\CompleteRequest;

/**
 * Class PurchaseComplete
 * @package App\Services\Payment\Events
 */
class PurchaseComplete
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var CompleteRequest
     */
    public $request;

    /**
     * PurchaseComplete constructor.
     *
     * @param CompleteRequest $request
     */
    public function __construct(CompleteRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->request->getPaymentId();
    }
}