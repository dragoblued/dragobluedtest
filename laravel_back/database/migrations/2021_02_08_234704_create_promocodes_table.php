<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromocodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            /* percent, numeric */
            $table->string('discount_type')->nullable();
            $table->integer('discount')->unsigned()->nullable();
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->integer('usage_limit')->default(1);
            $table->integer('usage_count')->default(0);
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('subject_id')->unsigned()->nullable();
            $table->string('subject_type')->nullable();
            $table->integer('group_id')->unsigned()->nullable();
            $table->timestamps();

           $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('set null');

            $table->foreign('group_id')
                ->references('id')
                ->on('groupes')
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
        Schema::dropIfExists('promocodes');
    }
}
