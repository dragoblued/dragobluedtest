<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('courses', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('user_creator_id')->unsigned()->nullable();
         $table->string('route')->unique();
         $table->string('name')->unique();
         /* editing, published, coming-soon  */
         $table->string('status')->nullable();
         $table->string('lang')->nullable();
         /* будет ли отображаться модель челюсти в описании курса */
         $table->boolean('is_model_visible')->default(true);
         $table->integer('order')->default(100);
         $table->string('title');
         $table->string('sub_title')->nullable();
         $table->string('subsign')->nullable();
         $table->longText('description')->nullable();
         $table->longText('faq')->nullable();
         $table->string('tags')->nullable();


         /* Image */
         $table->string('poster_url')->nullable();

         /* Promo video */
         $table->string('promo_video_url')->nullable();
         $table->string('promo_video_original_name')->nullable(); // original file name
         $table->integer('promo_video_duration')->nullable();
         $table->string('promo_video_available_formats')->nullable();

         /* Success video convert */
         $table->boolean('converted')->default(false); // if compile convert -> true
         $table->datetime('converted_at')->nullable(); // if compile convert -> time

         $table->integer('topics_count')->default(0);
         // обновлять при каждом добавлении\редактировании соответсвующего топика
         $table->integer('lessons_count')->default(0);
         // обновлять при каждом добавлении\редактировании соответсвующего урока
         $table->integer('total_lessons_duration')->default(0);
         $table->longText('refund_policy')->nullable();

         /* Students related info */
         $table->longText('what_will_students_learn')->nullable();
         $table->text('target_students')->nullable();
         $table->longText('requirements')->nullable();

         /* Price */
         $table->double('actual_price', 10, 2)->nullable();
         $table->double('discount_price', 10, 2)->nullable();

         /* Statistics */
         $table->integer('positive_votes_count')->default(0);
         $table->integer('negative_votes_count')->default(0);
         $table->integer('view_count')->default(0);
         $table->integer('purchase_count')->default(0);

         $table->timestamps();

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
      Schema::dropIfExists('courses');
   }
}
