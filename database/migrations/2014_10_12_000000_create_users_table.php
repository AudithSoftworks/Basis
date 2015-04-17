<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password', 255); // 255 instead of 60, for forward-compatibility with PASSWORD_DEFAULT constant
            $table->rememberToken();
            $table->integer('created_at', false, true);
            $table->integer('updated_at', false, true)->nullable();
            $table->integer('deleted_at', false, true)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Schema::drop('users');
    }
}
