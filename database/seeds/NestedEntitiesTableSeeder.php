<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Schema\Blueprint;

class NestedEntitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::dropIfExists('nested_entities');
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

        DB::table('nested_entities')->insert(
            array(
                'id' => 1,
                'name' => 'Root',
                'left_range' => 1,
                'right_range' => 2,
                'created_at' => time()
            )
        );
    }
}
