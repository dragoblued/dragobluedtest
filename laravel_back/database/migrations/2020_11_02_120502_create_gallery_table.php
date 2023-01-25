<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalleryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            // Image or video 'image', 'video'
            $table->string('mime_type')->nullable();
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            // Постер для видео
            $table->string('poster_url')->nullable();
            // Доступные форматы для видео
            $table->string('available_formats')->nullable();
            /* 0 - video not uploaded
               1 - video mp4 converted
               2 - video hls converted
               3 - video is converting
               4 - convertation errored
            */
            $table->integer('converted')->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gallery');
    }
}
