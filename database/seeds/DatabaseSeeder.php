<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Eloquent::unguard();

        $this->call('NestedEntitiesTableSeeder');
        $this->command->info('NestedEntities table created and seeded.');
    }

}
