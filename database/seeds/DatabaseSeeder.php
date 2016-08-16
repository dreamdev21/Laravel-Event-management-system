<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Eloquent::unguard();

        $this->call('CountriesSeeder');
        $this->call('CurrencySeeder');
        $this->call('OrderStatusSeeder');
        $this->call('PaymentGatewaySeeder');
        $this->call('QuestionTypesSeeder');
        $this->call('TicketStatusSeeder');
        $this->call('TimezoneSeeder');
    }
}
