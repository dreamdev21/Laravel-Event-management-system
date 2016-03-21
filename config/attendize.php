<?php

return [

    'enable_test_payments' => env('ENABLE_TEST_PAYMENTS', false),

    'payment_gateway_stripe'   => 1,
    'payment_gateway_paypal'   => 2,
    'payment_gateway_coinbase' => 3,

    'outgoing_email_noreply' => env('MAIL_FROM_ADDRESS'),
    'outgoing_email' => env('MAIL_FROM_ADDRESS'),
    'outgoing_email_name' => env('MAIL_FROM_NAME'),
    'incoming_email' => env('MAIL_FROM_ADDRESS'),

    'app_name' => 'Attendize Event Ticketing',
    'event_default_bg_color' => '#B23333',

    'event_images_path' => 'user_content/event_images',
    'organiser_images_path' => 'user_content/organiser_images',
    'event_pdf_tickets_path' => 'user_content/pdf_tickets',
    'event_bg_images' => 'assets/images/public/EventPage/backgrounds',

    'fallback_organiser_logo_url' => '/assets/images/logo-100x100-lightBg.png',
    'cdn_url' => '',

    'checkout_timeout_after' => app()->environment('local', 'development') ? 30 : 8, #mintutes

    'ticket_status_sold_out' => 1,
    'ticket_status_after_sale_date' => 2,
    'ticket_status_before_sale_date' => 3,
    'ticket_status_on_sale' => 4,
    'ticket_status_off_sale' => 5,

    'ticket_booking_fee_fixed' => 0,
    'ticket_booking_fee_percentage' => 0,

    'order_complete' => 1,
    'order_refunded' => 2,
    'order_partially_refunded' => 3,
    'order_cancelled' => 4,

    'default_timezone' => 30, #Europe/Dublin
    'default_currency' => 2, #Euro
    'default_date_format' => 'j M, Y',
    'default_date_picker_format' => 'd M, yyyy',
    'default_datetime_format' => 'F j, Y, g:i a',
    'default_query_cache' => 120, #Minutes
    'default_locale' => 'en',
    'default_payment_gateway' => 1, #Stripe=1 Paypal=2 BitPay=3

    'cdn_url_user_assets' => '',
    'cdn_url_static_assets' => ''
];
