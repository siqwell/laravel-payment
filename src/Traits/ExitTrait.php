<?php
namespace Siqwell\Payment\Traits;

/**
 * Trait ExitTrait
 * @package App\Services\Payment\Traits
 */
trait ExitTrait
{
    /**
     * @codeCoverageIgnore
     *
     * @param string $result
     * @param array  $headers
     */
    public function exit($result = '', array $headers = [])
    {
        if (is_array($headers)) {
            foreach ($headers as $header) {
                header($header);
            }
        } else {
            header('Content-Type: text/plain; charset=utf-8');
        }

        die($result);
    }
}