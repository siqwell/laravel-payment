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
        ],
        [
            'key'         => 'wme',
            'name'        => 'WebMoney EUR',
            'driver'      => 'WebMoney',
            'currency_id' => 978,
            'is_active'   => true,
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
