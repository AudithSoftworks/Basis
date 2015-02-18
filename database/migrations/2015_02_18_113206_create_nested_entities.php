<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNestedEntities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nested_entities', function (Blueprint $table) {
            $table->engine = 'InnoDb';

            $table->mediumInteger('id', true, true);
            $table->string('name', 255);
            $table->mediumInteger('left_range', false, true);
            $table->mediumInteger('right_range', false, true);
            $table->integer('created_at', false, true);
            $table->integer('updated_at', false, true)->nullable();
            $table->integer('deleted_at', false, true)->nullable();

            $table->unique(array('left_range', 'right_range'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nested_entities');
    }
}
