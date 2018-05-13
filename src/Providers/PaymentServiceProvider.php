<?php
namespace Siqwell\Payment\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Siqwell\Payment\Commands\CurrencyClear;
use Siqwell\Payment\Commands\CurrencyUpdate;
use Siqwell\Payment\DriverFactory;
use Siqwell\Payment\PaymentService;
use Swap\Laravel\SwapServiceProvider;

/**
 * Class PaymentServiceProvider
 * @package Siqwell\Payment\Providers
 */
class PaymentServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // \App\Services\Payment\Events\PurchaseComplete::class => [],
        // \App\Services\Payment\Events\PurchaseFailed::class => [],
        // \App\Services\Payment\Events\PurchaseStart::class => [],
    ];

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'command.payment.currency-update',
        'command.payment.currency-clear',
    ];

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../../database/seeds/' => database_path('seeds')
        ], 'seeds');
    }

    /**
     * Register Payment Service
     */
    public function register()
    {
        $this->registerService();
        $this->registerEvents();
        $this->registerCommands();

        $this->app->register(SwapServiceProvider::class);
    }

    /**
     * Register the service provider.
     */
    public function registerService()
    {
        $this->app->singleton('payment', function ($app) {
            return new PaymentService($app, new DriverFactory());
        });

        $this->app->alias('payment', PaymentService::class);
    }

    /**
     * Register the Service event listeners.
     */
    public function registerEvents()
    {
        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }

    /**
     * Register the given commands.
     */
    public function registerCommands()
    {
        $this->app->singleton('command.payment.currency-update', function () {
            return new CurrencyUpdate();
        });

        $this->app->singleton('command.payment.currency-clear', function () {
            return new CurrencyClear();
        });

        $this->commands($this->commands);
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens(): array
    {
        return $this->listen;
    }

    /**
     * Get the services provided.
     *
     * @return array
     */
    public function provides(): array
    {
        return $this->commands;
    }
}