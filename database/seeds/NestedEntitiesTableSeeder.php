<?php

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
        \Eloquent::unguarded(function () {
            DB::table('nested_entities')->truncate();
            DB::table('nested_entities')->insert(
                array(
                    'name' => 'Root',
                    'left_range' => 1,
                    'right_range' => 2,
                    'created_at' => time()
                )
            );
        });
        $this->command->info('NestedEntitiesTable seeded.');
    }
}
