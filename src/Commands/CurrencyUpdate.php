<?php
namespace Siqwell\Payment\Commands;

use Siqwell\Payment\Jobs\CurrencyUpdateJob;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Siqwell\Payment\Entities\Currency;

/**
 * Class CurrencyUpdate
 * @package App\Console\Commands
 */
class CurrencyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:currency-update {date?} {--fixed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency exchange rate values';

    /**
     * @var Collection
     */
    protected $currency;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->currency = Currency::active()->get(['id', 'code']);

        if ($date = $this->argument('date')) {
            $date = Carbon::createFromFormat('Y-m-d', $date);
        }

        if ($this->option('fixed') && $date instanceof Carbon) {
            while ($date->lt(Carbon::now())) {
                $this->updateCurrencyOfDay($date);
                $date->addDay();
            }
        } else {
            $this->updateCurrencyOfDay(Carbon::now());
        }
    }

    /**
     * @param Carbon $day
     */
    private function updateCurrencyOfDay(Carbon $day)
    {
        $this->currency->each(function (Currency $from) use ($day) {
            $this->currency->each(function (Currency $to) use ($from, $day) {
                if ($from->getKey() === $to->getKey()) {
                    return;
                }

                CurrencyUpdateJob::dispatch($day, $from, $to);

                $this->info("Release update " . $from->getCode() . '/' . $to->getCode() . " of day: " . $day->toDateString());
            });
        });
    }

}
