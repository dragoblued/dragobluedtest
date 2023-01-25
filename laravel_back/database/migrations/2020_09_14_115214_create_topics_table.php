<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned()->nullable();
            $table->integer('user_creator_id')->unsigned()->nullable();
            $table->string('route')->unique();
            $table->string('name')->unique();
            $table->string('status')->nullable();
            $table->string('lang')->nullable();
            $table->integer('order')->default(100);
            $table->string('title');
            $table->string('sub_title')->nullable();
            $table->longText('description')->nullable();

            /* Image */
            $table->string('poster_url')->nullable();

            /* Promo video */
            $table->string('promo_video_url')->nullable();
            $table->string('promo_video_original_name')->nullable(); // hidden
            $table->integer('promo_video_duration')->nullable(); // hidden
            $table->string('promo_video_available_formats')->nullable(); // hidden
            /* Success video convert */
            $table->boolean('converted')->default(false); // hidden
            $table->datetime('converted_at')->nullable(); // hidden

            $table->integer('lessons_count')->default(0); // hidden
            // обновлять при каждом добавлении\редактировании соответсвующего урока
            $table->integer('total_lessons_duration')->default(0); // hidden
            // обновлять при каждом добавлении\редактировании соответсвующего урока

            /* Price */
            $table->boolean('is_free')->default(false);
            // checkbox
            $table->double('actual_price', 10, 2)->nullable();
            $table->double('discount_price', 10, 2)->nullable();

            /* Statistics */
            $table->integer('positive_votes_count')->default(0)->nullable();
            $table->integer('negative_votes_count')->default(0)->nullable();
            $table->integer('view_count')->default(0)->nullable();
            $table->integer('purchase_count')->default(0)->nullable();

            $table->timestamps();

            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('set null');

            $table->foreign('user_creator_id')
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
        Schema::dropIfExists('topics');
    }
}
