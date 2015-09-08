<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Part of the Sentinel package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package        Sentinel
 * @version        2.0.6
 * @author         Cartalyst LLC
 * @license        BSD License (3-clause)
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link           http://cartalyst.com
 */
class CartalystSentinel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->text('permissions')->nullable()->after('password');
            $table->timestamp('last_login')->nullable()->after('remember_token');
            $table->dropColumn('remember_token');
        });

        Schema::create('activations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('code');
            $table->boolean('completed')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('persistences', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('code')->unique();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('code');
            $table->boolean('completed')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('permissions')->nullable();
            $table->timestamps();
        });

        Schema::create('role_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');
            $table->nullableTimestamps();

            $table->primary(['user_id', 'role_id']);
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('throttle', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('type');
            $table->string('ip')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['permissions', 'last_login']);
            $table->rememberToken();
        });
        Schema::drop('activations');
        Schema::drop('persistences');
        Schema::drop('reminders');
        Schema::drop('role_users');
        Schema::drop('roles');
        Schema::drop('throttle');
    }
}
