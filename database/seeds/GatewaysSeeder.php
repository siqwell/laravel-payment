<?php

use Illuminate\Database\Seeder;
use Siqwell\Payment\Entities\Gateway;

/**
 * Class GatewaysSeeder
 */
class GatewaysSeeder extends Seeder
{
    /**
     * @var array
     */
    protected $gateways = [
        [
            'key'         => 'wmz',
            'name'        => 'WebMoney USD',
            'driver'      => 'WebMoney',
            'currency_id' => 840,
            'is_active'   => true,
            'params'      => '{}',
        ],
        [
            'key'         => 'wme',
            'name'        => 'WebMoney EUR',
            'driver'      => 'WebMoney',
            'currency_id' => 978,
            'is_active'   => true,
            'params'      => '{}',
        ],
        [
            'key'         => 'coin',
            'name'        => 'CoinPayment',
            'driver'      => 'Coin',
            'currency_id' => 840,
            'is_active'   => true,
            'params'      => '{"currency2":643}',
        ],
        [
            'key'         => 'pb',
            'name'        => 'PayBoutique',
            'driver'      => 'PayBoutique',
            'currency_id' => 840,
            'is_active'   => true,
            'params'      => '{}',
        ],
        [
            'key'         => 'tp',
            'name'        => 'TransactPro',
            'driver'      => 'TransactPro',
            'currency_id' => 840,
            'is_active'   => true,
            'params'      => '{}',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect($this->gateways)->each(function (array $gateway) {
            Gateway::updateOrCreate(['key' => $gateway['key']], $gateway);
        });
    }
}
