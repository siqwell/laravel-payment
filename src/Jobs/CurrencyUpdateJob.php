<?php

namespace Siqwell\Payment\Jobs;

use Carbon\Carbon;
use Exchanger\Exception\ChainException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Siqwell\Payment\Entities\Course;
use Siqwell\Payment\Entities\Currency;
use Swap\Laravel\Facades\Swap;

/**
 * Class CurrencyUpdateJob
 * @package App\Jobs
 */
class CurrencyUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Carbon
     */
    private $date;

    /**
     * @var Currency
     */
    private $from;

    /**
     * @var Currency
     */
    private $to;

    /**
     * CurrencyUpdate constructor.
     *
     * @param Carbon   $date
     * @param Currency $from
     * @param Currency $to
     */
    public function __construct(Carbon $date, Currency $from, Currency $to)
    {
        $this->date = $date;
        $this->from = $from;
        $this->to   = $to;
    }

    /**
     * @return void
     */
    public function handle()
    {
        try {
            if ($this->date->toDateString() == Carbon::now()->toDateString()) {
                /** @var \Exchanger\ExchangeRate $rate */
                $rate = Swap::latest($this->from->getCode() . '/' . $this->to->getCode());
            } else {
                /** @var \Exchanger\ExchangeRate $rate */
                $rate = Swap::historical($this->from->getCode() . '/' . $this->to->getCode(), $this->date);
            }
        } catch (ChainException $exception) {
            $this->release(3);

            return;
        }

        Course::updateOrCreate([
            'from' => $this->from->getKey(),
            'to'   => $this->to->getKey(),
        ], [
            'date' => $rate->getDate()->format('Y-m-d'),
            'value' => $rate->getValue(),
        ]);
    }
}
