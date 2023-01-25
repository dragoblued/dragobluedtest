<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('tickets', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('user_id')->unsigned()->nullable();
         $table->bigInteger('date_id')->unsigned()->nullable();
         $table->integer('count')->unsigned()->default(1);
         $table->boolean('is_purchased')->default(false);
         $table->boolean('is_canceled')->default(false);
         $table->boolean('is_expired')->default(false);
         $table->boolean('is_reminded')->default(false);
         $table->text('recipient_persons')->nullable();
         $table->bigInteger('invoice_id')->unsigned()->nullable();
         $table->timestamps();

         $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('set null');

         $table->foreign('date_id')
            ->references('id')
            ->on('dates')
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
      Schema::dropIfExists('tickets');
   }
}
