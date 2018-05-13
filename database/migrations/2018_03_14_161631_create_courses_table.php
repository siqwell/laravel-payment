<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateCoursesTable
 */
class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var \Siqwell\Payment\Entities\Course */
        Schema::create('courses', function (Blueprint $table) {
            $table->string('date');

            $table->unsignedInteger('from');
            $table->unsignedInteger('to');

            $table->float('value', 15, 8);

            $table->foreign('from')->references('id')->on('currencies')->onDelete('cascade');
            $table->foreign('to')->references('id')->on('currencies')->onDelete('cascade');

            $table->unique(['from', 'to', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
