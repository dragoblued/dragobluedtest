<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRoomTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('user_room', function (Blueprint $table) {
         $table->bigIncrements('id');
         $table->integer('user_id')->unsigned()->nullable();
         $table->integer('room_id')->unsigned()->nullable();
         $table->timestamps();

         $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('set null');

         $table->foreign('room_id')
            ->references('id')
            ->on('rooms')
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
      Schema::dropIfExists('user_room');
   }
}
