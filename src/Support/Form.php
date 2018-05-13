<?php
namespace Siqwell\Payment\Support;

/**
 * Class Form
 * @package Siqwell\Payment\Support
 */
class Form
{
    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $params = [];

    /**
     * Form constructor.
     *
     * @param string $action
     * @param array  $params
     * @param string $method
     */
    public function __construct(string $action, array $params = [], string $method = 'POST')
    {
        $this->action = $action;
        $this->params = $params;
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'action' => $this->action,
            'method' => $this->method,
            'params' => $this->params,
        ];
    }
}