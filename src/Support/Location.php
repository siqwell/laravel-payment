<?php
namespace Siqwell\Payment\Support;

/**
 * Class Form
 * @package Siqwell\Payment\Support
 */
class Location
{
    /**
     * @var string
     */
    public $url;

    /**
     * Location constructor.
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->url;
    }
}