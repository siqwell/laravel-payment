<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateGatewaysTable
 */
class CreateGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \Siqwell\Payment\Entities\Gateway */
        Schema::create('gateways', function (Blueprint $table) {
            $table->increments('id');

            $table->string('key');
            $table->string('name');
            $table->string('driver');

            $table->unsignedInteger('currency_id');
            $table->boolean('is_active')->default(false);

            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gateways');
    }
}
