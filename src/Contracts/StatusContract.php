<?php
namespace Siqwell\Payment\Contracts;

/**
 * Interface StatusContract
 * @package Siqwell\Payment\Contracts
 */
interface StatusContract
{
    /** Statuses */
    const ACCEPT = 'accept',
        DECLINED = 'declined',
        CANCEL = 'cancel',
        PROCESS = 'process';
}