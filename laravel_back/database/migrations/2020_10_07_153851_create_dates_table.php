<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('event_id')->unsigned()->nullable();
            $table->year('year');
            $table->date('start');
            $table->date('end');
            $table->string('lang');
            $table->integer('seats_total')->nullable();
            $table->integer('seats_vacant')->default(0);
            $table->integer('seats_booked')->default(0);
            $table->integer('seats_purchased')->default(0);
            $table->boolean('is_expired')->default(false);

            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dates');
    }
}
