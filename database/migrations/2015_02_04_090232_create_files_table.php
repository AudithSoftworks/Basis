<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('files', function (Blueprint $table) {
            $table->engine = 'InnoDb';

            $table->integer('id', true, true);
            $table->string('hash', 32);
            $table->string('mime', 24);
            $table->bigInteger('size');
            $table->text('metadata');
            $table->integer('created_at', false, true);
            $table->integer('updated_at', false, true)->nullable();
            $table->integer('deleted_at', false, true)->nullable();

            $table->unique('hash');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::drop('files');
    }
}
