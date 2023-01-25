<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('test_id')->unsigned()->nullable();
            $table->string('type');
            /* select с опциями: ['single-choice', 'multiple-choice', 'fill-in-the-blanks'] */
            $table->longText('title');
            /* wisiwyg */
            $table->integer('number_of_options')->default(0);
            /* listen change event и выставлять колчисетво опций для options по этому значению */
            $table->longText('options')->nullable();
            /* '["option1", "option2", ...]'
            для 'single-choice', 'multiple-choice' - include type=string с множественными опциями
            для 'fill-in-the-blanks' - include type=textarea с описанием(на место куда вы хотите подставить поле выбора, подставьте @@) и предпросмотром(окошко ниже или сбоку textareaVAlue.replace(/@@/g, '______'))
            */
            $table->longText('correct_answers')->nullable();
            /* single-choice: '[3]'
             * multiple-choice: '[0, 2, 3]'
             * fill-in-the-blanks: '["qwerty", "asdfg", ...]'
             *
             * include type=string с множественными опциями
            */
            $table->integer('mark')->default(0);
            /* Количество баллов за вопрос */
            $table->timestamps();

            $table->foreign('test_id')
                ->references('id')
                ->on('tests')
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
        Schema::dropIfExists('test_questions');
    }
}
