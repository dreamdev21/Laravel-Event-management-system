<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Eloquent::unguard();

        
        $this->call('ConstantsSeeder');
        $this->command->info('Seeded the constants!');
        
        // $this->call('UserTableSeeder');
        $this->call('CountriesSeeder');
        $this->command->info('Seeded the countries!');
    }

}
