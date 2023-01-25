<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLessonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_lesson', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('lesson_id')->unsigned()->nullable();
            $table->integer('current_timing')->nullable();
            $table->integer('current_timing_percent')->nullable();
            $table->boolean('is_viewed')->default(false);
            $table->boolean('is_purchased')->default(false);
            $table->boolean('thumbnail')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('lesson_id')
                ->references('id')
                ->on('lessons')
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
        Schema::dropIfExists('user_lesson');
    }
}
