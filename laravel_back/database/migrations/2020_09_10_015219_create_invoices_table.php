<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('invoices', function (Blueprint $table) {
         $table->bigIncrements('id');
         $table->integer('user_id')->unsigned()->nullable();
         $table->string('method');
         $table->string('session_id')->nullable();
         $table->longText('session_object')->nullable();
         $table->longText('basket')->nullable();
         $table->double('price', 2)->nullable();
         $table->string('currency')->nullable();
         /* 0 - unpaid
         *  1 - paid and successfully processed
         *  3 - paid but process errored
         */
         $table->tinyInteger('state')->default(0);
         $table->string('receipt_url')->nullable();
         $table->boolean('paid_as_company')->default(false);
         $table->string('company_invoice_url')->nullable();
         $table->longText('additional_data')->nullable();
         $table->timestamps();

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
      Schema::dropIfExists('invoices');
   }
}
