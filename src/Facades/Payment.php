<?php
namespace Siqwell\Payment\Facades;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Siqwell\Payment\Contracts\PaymentContract;
use Siqwell\Payment\Contracts\PaymentInterface;
use Siqwell\Payment\Entities\Gateway;
use Siqwell\Payment\Requests\CompleteRequest;
use Siqwell\Payment\Requests\PurchaseRequest;

/**
 * Class Payment
 * @package Siqwell\Payment\Facades
 * @method static PurchaseRequest purchase(PaymentContract $payment, PaymentInterface $payment = null);
 * @method static CompleteRequest complete(Gateway $gateway, Request $request, PaymentInterface $payment = null);
 */
class Payment extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'payment';
    }
}