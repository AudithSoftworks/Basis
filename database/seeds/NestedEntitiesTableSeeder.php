<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NestedEntitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app('db.connection')->table('nested_entities')->truncate();
        app('db.connection')->table('nested_entities')->insert([
            'name' => 'Root',
            'left_range' => 1,
            'right_range' => 2,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
