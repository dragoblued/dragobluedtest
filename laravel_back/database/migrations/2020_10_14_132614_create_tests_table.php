<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('course_id')->unsigned()->nullable()->unique();
            $table->integer('duration')->unsigned()->nullable();
            /* Минуты */
            $table->integer('minimum_percentage')->unsigned();
            /* От 0 до 100 */
            $table->longText('instruction')->nullable();
            $table->integer('permitted_attempt_number')->default(-1);
            /* -1 означает неограниченное количество попыток */
            $table->integer('total_mark')->nullable();
            /* Суммарный балл за тест */
            $table->string('status')->nullable();
            /* select c опциями: 'editing'(по-умолчанию), 'published' */
            $table->timestamps();

            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
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
        Schema::dropIfExists('tests');
    }
}
