<?php

use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payment_gateways = [
            [
                'id' => 1,
                'name' => 'Stripe',
                'provider_name' => 'Stripe',
                'provider_url' => 'https://www.stripe.com',
                'is_on_site' => 1,
                'can_refund' => 1,
            ],
            [
                'id' => 2,
                'name' => 'PayPal_Express',
                'provider_name' => 'PayPal Express',
                'provider_url' => 'https://www.paypal.com',
                'is_on_site' => 0,
                'can_refund' => 0

            ],
            [
                'id' => 3,
                'name' => 'Coinbase',
                'provider_name' => 'Coinbase',
                'provider_url' => 'https://coinbase.com',
                'is_on_site' => 0,
                'can_refund' => 0,
            ],
            [
                'id' => 4,
                'name' => 'Migs_ThreeParty',
                'provider_name' => 'MasterCard Internet Gateway Service',
                'provider_url' => 'https://www.mastercard.com/gateway/payment-processing/online-credit-card-and-debit-card-payment-processing.html',
                'is_on_site' => 0,
                'can_refund' => 0,
            ],
        ];

        DB::table('payment_gateways')->insert($payment_gateways);

    }
}
