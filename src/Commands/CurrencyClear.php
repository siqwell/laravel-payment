<?php
namespace Siqwell\Payment\Commands;

use Illuminate\Console\Command;
use Siqwell\Payment\Entities\Course;

/**
 * Class CurrencyClear
 * @package App\Console\Commands
 */
class CurrencyClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:currency-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup currency exchange rate values';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Course::truncate();
    }
}
