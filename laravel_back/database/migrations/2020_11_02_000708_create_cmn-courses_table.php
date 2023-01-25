<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmnCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cmn-courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned()->nullable();
            $table->integer('event_id')->unsigned()->nullable();
            $table->string('route')->unique();
            $table->string('name')->unique();
            $table->string('title');
            $table->string('sub_title')->nullable();
            $table->string('subsign')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();

            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('set null');

            $table->foreign('event_id')
                ->references('id')
                ->on('events')
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
        Schema::dropIfExists('cmn-courses');
    }
}
