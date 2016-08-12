<?php

use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $order_statuses = [
            [
                'id' => 1,
                'name' => 'Completed',
            ],
            [
                'id' => 2,
                'name' => 'Refunded',
            ],
            [
                'id' => 3,
                'name' => 'Partially Refunded',
            ],
            [
                'id' => 4,
                'name' => 'Cancelled',
            ],
        ];

        DB::table('order_statuses')->insert($order_statuses);
    }
}
