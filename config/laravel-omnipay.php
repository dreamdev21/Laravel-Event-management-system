<?php

return [

    // The default gateway to use
    'default' => 'stripe',

    // Add in each gateway here
    'gateways' => [
        'paypal' => [
            'driver'  => 'PayPal_Express',
            'options' => [
                'solutionType'   => '',
                'landingPage'    => '',
                'headerImageUrl' => '',
            ],
        ],
        'stripe' => [
            'driver'  => 'Stripe',
            'options' => [],
        ],
    ],

];
