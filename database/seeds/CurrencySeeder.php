<?php

use Illuminate\Database\Seeder;
use Siqwell\Payment\Entities\Currency;

/**
 * Class CurrenciesSeeder
 */
class CurrencySeeder extends Seeder
{
    /**
     * @var array
     */
    private $currencies = [
        [
            'id'             => 840,
            'title'          => 'U.S. Dollar',
            'symbol_left'    => '$ ',
            'symbol_right'   => '',
            'code'           => 'USD',
            'decimal_place'  => 2,
            'decimal_point'  => '.',
            'thousand_point' => ',',
            'is_active'      => 1,
        ],
        [
            'id'             => 978,
            'title'          => 'Euro',
            'symbol_left'    => '€ ',
            'symbol_right'   => '',
            'code'           => 'EUR',
            'decimal_place'  => 2,
            'decimal_point'  => '.',
            'thousand_point' => ',',
            'is_active'      => 1,
        ],
        [
            'id'             => 643,
            'title'          => 'Russian ruble',
            'symbol_left'    => '',
            'symbol_right'   => ' ₽',
            'code'           => 'RUB',
            'decimal_place'  => 2,
            'decimal_point'  => '.',
            'thousand_point' => ',',
            'is_active'      => 1,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect($this->currencies)->each(function (array $currency) {
            Currency::updateOrCreate(['id' => $currency['id']], $currency);
        });
    }
}
