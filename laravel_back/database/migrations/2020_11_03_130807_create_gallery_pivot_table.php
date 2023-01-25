<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalleryPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery_pivot', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order')->nullable()->default(0);
            $table->integer('gallery_id')->unsigned();
            $table->integer('event_id')->unsigned()->nullable();
            $table->integer('course_id')->unsigned()->nullable();
            $table->integer('page_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('gallery_id')
                ->references('id')
                ->on('gallery')
                ->onDelete('cascade');

            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');

            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');

            $table->foreign('page_id')
                ->references('id')
                ->on('pages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gallery_pivot');
    }
}
