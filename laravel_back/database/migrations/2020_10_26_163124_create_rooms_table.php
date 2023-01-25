<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subject_id')->unsigned()->nullable();
            $table->string('subject_type')->nullable();
            $table->integer('creator_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->timestamps();

           $table->foreign('creator_id')
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
        Schema::dropIfExists('rooms');
    }
}
