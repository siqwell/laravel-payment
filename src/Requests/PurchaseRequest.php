<?php
namespace Siqwell\Payment\Requests;

use Siqwell\Payment\Support\Form;
use Siqwell\Payment\Support\Location;

/**
 * Class PurchaseRequest
 * @package Siqwell\Payment\Requests
 */
class PurchaseRequest
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Location
     */
    protected $location;

    /**
     * @var bool
     */
    protected $result;

    /**
     * AbstractRequest constructor.
     *
     * @param null $result
     */
    public function __construct($result = null)
    {
        $this->result = true;

        if ($result instanceof Form) {
            $this->form = $result;
        } elseif ($result instanceof Location) {
            $this->location = $result;
        } else {
            $this->result = false;
        }
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->result;
    }

    /**
     * @return bool
     */
    public function isRedirect(): bool
    {
        return isset($this->location);
    }

    /**
     * @return string
     */
    public function getRedirect(): string
    {
        return $this->location->toString();
    }

    /**
     * @return bool
     */
    public function isForm(): bool
    {
        return isset($this->form);
    }

    /**
     * @return array
     */
    public function getForm(): array
    {
        return $this->form->toArray();
    }
}