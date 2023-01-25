<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('topic_id')->unsigned()->nullable();
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
            $table->string('poster_url')->nullable(); // upload

            /* Full video */
            $table->string('video_url')->nullable(); // upload
            $table->string('video_original_name')->nullable();
            $table->integer('video_duration')->nullable();
            $table->string('video_available_formats')->nullable();
            $table->string('video_type')->nullable();
            $table->integer('video_size')->nullable();

            /* Promo video */
            $table->string('promo_video_url')->nullable(); // upload
            $table->string('promo_video_original_name')->nullable();
            $table->integer('promo_video_duration')->nullable();
            $table->string('promo_video_available_formats')->nullable();
            $table->integer('promo_video_size')->nullable();

            /* Success video convert */
           /* 0 - video not uploaded
              1 - video mp4 converted
              2 - video hls converted
              3 - video is converting
              4 - convertation errored
           */
            $table->integer('converted')->default(0);
            $table->string('converting_progress')->nullable();
            $table->datetime('converted_at')->nullable();

            /* Price */
            $table->boolean('is_free')->default(false);

            /* Statistics */
            $table->integer('positive_votes_count')->default(0)->nullable();
            $table->integer('negative_votes_count')->default(0)->nullable();
            $table->integer('purchase_count')->default(0)->nullable();
            $table->integer('view_count')->default(0)->nullable();

            $table->timestamps();

            $table->foreign('topic_id')
                ->references('id')
                ->on('topics')
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
        Schema::dropIfExists('lessons');
    }
}
