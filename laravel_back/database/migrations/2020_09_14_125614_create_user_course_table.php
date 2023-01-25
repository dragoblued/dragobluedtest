<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCourseTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('user_course', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('user_id')->unsigned()->nullable();
         $table->integer('course_id')->unsigned()->nullable();
         $table->integer('lessons_view_count')->unsigned()->default(0);
         $table->boolean('is_purchased')->default(false);
         $table->bigInteger('invoice_id')->unsigned()->nullable();
         $table->timestamps();

         $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('set null');

         $table->foreign('course_id')
            ->references('id')
            ->on('courses')
            ->onDelete('set null');

         $table->foreign('invoice_id')
            ->references('id')
            ->on('invoices')
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
      Schema::dropIfExists('user_course');
   }
}
