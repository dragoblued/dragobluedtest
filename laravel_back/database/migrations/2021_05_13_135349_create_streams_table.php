<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('key')->nullable();
            $table->string('lang')->nullable();
            $table->string('title');
            $table->string('sub_title')->nullable();
            $table->longText('description')->nullable();
            $table->integer('broadcaster_id')->unsigned()->nullable();

            /* 0 - pending
               1 - broadcasting
               2 - broadcasting paused
               3 - broadcasting ended
               4 - broadcasting errored
            */
            $table->integer('status')->default(0);
            $table->boolean('is_expired')->default(false);
            $table->longText('allowed_users')->nullable();
            $table->longText('banned_users')->nullable();

            $table->datetime('appointment_datetime')->nullable();
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();

            /* Poster */
            $table->string('poster_url')->nullable();

            /* Recorded video */
            $table->string('recorded_video_url')->nullable();
            $table->string('recorded_video_original_name')->nullable();
            $table->integer('recorded_video_duration')->nullable();
            $table->string('recorded_video_available_formats')->nullable();
            $table->string('recorded_video_type')->nullable();
            $table->integer('recorded_video_size')->nullable();
            /* 0 - video not uploaded
               1 - video mp4 converted
               2 - video hls converted
               3 - video is converting
               4 - convertation errored
            */
            $table->integer('recorded_video_converted')->default(0);
            $table->datetime('recorded_video_converted_at')->nullable();

            /* Price */
            $table->boolean('is_free')->default(false);
            $table->double('actual_price', 10, 2)->nullable();
            $table->double('discount_price', 10, 2)->nullable();
            $table->integer('seats_total')->nullable();
            $table->integer('seats_vacant')->nullable();
            $table->integer('seats_booked')->nullable();
            $table->integer('seats_purchased')->nullable();

            /* Statistics */
            $table->integer('positive_votes_count')->default(0)->nullable();
            $table->integer('negative_votes_count')->default(0)->nullable();
            $table->integer('purchase_count')->default(0)->nullable();
            $table->integer('view_count')->default(0)->nullable();

            $table->timestamps();

            $table->foreign('broadcaster_id')
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
        Schema::dropIfExists('streams');
    }
}
