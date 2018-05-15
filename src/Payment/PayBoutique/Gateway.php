<?php

namespace Siqwell\Payment\PayBoutique;

use App\Exceptions\GatewayException;
use Illuminate\Http\Request;
use Siqwell\Payment\BaseDriver;
use Siqwell\Payment\Contracts\PaymentContract;

/**
 * Class PayBoutique
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
        return [
            'transactionId' => $contract->getId(),
            'description'   => $contract->getDescription(),
            'amount'        => $contract->getAmount(),
            'returnUrl'     => $contract->getSuccessUrl(),
            'cancelUrl'     => $contract->getFailedUrl(),
            //TODO ResultUrl???
            'notifyUrl'     => $contract->getUrl('notify'),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws GatewayException
     */
    public function complete(Request $request): array
    {
        $content = $request->getContent();

        try {
            parse_str($content, $data);
        } catch (\Exception $exception) {
            throw new GatewayException("Failed to parse XML");
        }

        return [
            'transactionReference' => $data['xml']
        ];
    }
}