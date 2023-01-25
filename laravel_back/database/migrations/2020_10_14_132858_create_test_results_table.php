<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->longText('answer_script')->nullable();
            $table->text('obtained_marks')->nullable();
            $table->integer('max_mark')->nullable();
            $table->integer('max_mark_percent')->nullable();
            $table->string('status')->nullable();
            /* ['in-progress', 'finished'] */
            $table->dateTime('test_started_timestamp')->nullable();
            $table->integer('attempt_number')->default(1);
            $table->string('result')->nullable();
            $table->timestamps();

            $table->foreign('test_id')
                ->references('id')
                ->on('tests')
                ->onDelete('set null');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_results');
    }
}
