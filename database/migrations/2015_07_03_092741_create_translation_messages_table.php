<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translation_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->integer('category_id', false, true);
            $table->text('message');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('translation_categories')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('translation_messages');
    }
}
