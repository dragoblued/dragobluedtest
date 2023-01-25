<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
   /**
    * Run the migrations.
    * Api registration tables: activation_token, softDeletes
    * Api registration rule: email confirm link with table activation_token -> make table activate = true
    *
    * @return void
    */
   public function up()
   {
      Schema::create('users', function (Blueprint $table) {
         $table->increments('id');
         $table->boolean('active')->default(false);
         $table->string('activation_token')->nullable();
         $table->string('login')->nullable()->unique();
         $table->string('email')->unique();
         $table->string('name')->nullable();
         $table->string('surname')->nullable();
         $table->string('middle_name')->nullable();
         $table->string('avatar_url')->nullable();
         $table->string('social_id')->nullable();
         $table->string('phone')->nullable();
         $table->text('address')->nullable();
         $table->string('zip')->nullable();
         $table->boolean('need_company')->nullable();
         $table->longText('company_info')->nullable();
         $table->text('device_ids')->nullable();
         $table->string('password');
         $table->string('api_token', 80)->unique()->nullable()->default(null);

         $table->integer('role_id')->unsigned()->nullable();
         $table->timestamp('email_verified_at')->nullable();
         $table->rememberToken();
         $table->timestamps();
         $table->softDeletes();

         $table->foreign('role_id')
            ->references('id')
            ->on('roles')
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
      Schema::dropIfExists('users');
   }
}
