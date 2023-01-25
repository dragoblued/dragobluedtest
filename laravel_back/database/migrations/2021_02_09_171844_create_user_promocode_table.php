<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPromocodeTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('user_promocode', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('user_id')->unsigned()->nullable();
         $table->integer('promocode_id')->unsigned()->nullable();
         $table->integer('applied_count')->default(1);
         $table->timestamps();

         $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('set null');

         $table->foreign('promocode_id')
            ->references('id')
            ->on('promocodes')
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
      Schema::dropIfExists('user_promocode');
   }
}
