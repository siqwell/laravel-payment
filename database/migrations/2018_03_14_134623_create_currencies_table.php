<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateCurrenciesTable
 */
class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \Siqwell\Payment\Entities\Currency*/
        Schema::create('currencies', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->string('title', 255);
            $table->string('code', 3);
            $table->string('symbol_left', 12);
            $table->string('symbol_right', 12);
            $table->integer('decimal_place');
            $table->string('decimal_point', 3);
            $table->string('thousand_point', 3);
            $table->boolean('is_active')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
