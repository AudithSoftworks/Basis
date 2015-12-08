<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files_users', function (Blueprint $table) {
            $table->engine = 'InnoDb';
            $table->string('file_hash', 64);
            $table->unsignedInteger('user_id');
            $table->string('uuid', 36)->nullable();
            $table->string('original_client_name');
            $table->string('tag');
            $table->timestamps();

            $table->foreign('file_hash')->references('hash')->on('files')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files_users', function (Blueprint $table) {
            $table->dropForeign('files_users_file_hash_foreign');
            $table->dropForeign('files_users_user_id_foreign');
        });
        Schema::drop('files_users');
    }
}
