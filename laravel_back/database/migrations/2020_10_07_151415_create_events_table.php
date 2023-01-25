<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('events', function (Blueprint $table) {
         $table->increments('id');
         $table->string('route')->unique();
         $table->string('name')->unique();
         $table->string('status')->nullable();
         $table->boolean('is_model_visible')->default(true);
         $table->integer('order')->default(100);
         $table->string('title');
         $table->string('sub_title')->nullable();
         $table->string('subsign')->nullable();
         $table->longText('description')->nullable();
         // wisiwyg
         $table->integer('duration')->nullable();
         // Продолжительность в днях
         $table->string('langs')->nullable();
         $table->longText('plan')->nullable();
         $table->longText('program')->nullable();
         // Массив массивов
         $table->string('poster_url')->nullable();
         $table->string('model_url')->nullable();
         $table->string('collage_url')->nullable();

         // пропуск, в доработке Uploader
         $table->string('promo_video_url')->nullable();
         $table->string('promo_video_original_name')->nullable(); // hidden
         $table->integer('promo_video_duration')->nullable(); // hidden
         $table->string('promo_video_available_formats')->nullable();

         /* Success video convert */
         $table->boolean('converted')->default(false); // hidden
         $table->datetime('converted_at')->nullable(); // hidden

         $table->text('address')->nullable();
         $table->text('address_building_name')->nullable();
         $table->string('address_url')->nullable();
         $table->string('address_coordinates')->nullable();
         $table->longText('address_howtoreach')->nullable();
         // wisiwyg

         /* Price */
         $table->double('actual_price', 10, 2)->nullable();
         // Добавить в конец поля значок валюты из таблицы settings
         $table->double('discount_price', 10, 2)->nullable();
         // Добавить в конец поля значок валюты из таблицы settings

         /* Statistics */
         // пропуск
         $table->integer('positive_votes_count')->default(0)->nullable();
         $table->integer('negative_votes_count')->default(0)->nullable();
         $table->integer('view_count')->default(0)->nullable();
         $table->integer('bought_tickets_count')->default(0)->nullable();

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
      Schema::dropIfExists('events');
   }
}
