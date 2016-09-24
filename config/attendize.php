<?php

return [

    'version' => file_get_contents(base_path('VERSION')),

    'ticket_status_sold_out'        => 1,
    'ticket_status_after_sale_date' => 2,//
    'enable_test_payments'          => env('ENABLE_TEST_PAYMENTS', false),

    'payment_gateway_stripe'   => 1,
    'payment_gateway_paypal'   => 2,
    'payment_gateway_coinbase' => 3,
	'payment_gateway_migs'     => 4,

    'outgoing_email_noreply' => env('MAIL_FROM_ADDRESS'),
    'outgoing_email'         => env('MAIL_FROM_ADDRESS'),
    'outgoing_email_name'    => env('MAIL_FROM_NAME'),
    'incoming_email'         => env('MAIL_FROM_ADDRESS'),

    'app_name'               => 'Attendize Event Ticketing',
    'event_default_bg_color' => '#B23333',
    'event_default_bg_image' => 'assets/images/public/EventPage/backgrounds/5.jpg',

    'event_images_path'      => 'user_content/event_images',
    'organiser_images_path'  => 'user_content/organiser_images',
    'event_pdf_tickets_path' => 'user_content/pdf_tickets',
    'event_bg_images'        => 'assets/images/public/EventPage/backgrounds',

    'fallback_organiser_logo_url' => '/assets/images/logo-dark.png',
    'cdn_url'                     => '',

    'checkout_timeout_after' => env('CHECKOUT_TIMEOUT_AFTER', 10), #minutes

    'ticket_status_before_sale_date' => 3,
    'ticket_status_on_sale'          => 4,
    'ticket_status_off_sale'         => 5,

    'ticket_booking_fee_fixed'      => 0,
    'ticket_booking_fee_percentage' => 0,

    /* Order statuses */
    'order_complete'                => 1,
    'order_refunded'                => 2,
    'order_partially_refunded'      => 3,
    'order_cancelled'               => 4,
    'order_awaiting_payment'        => 5,

    /* Attendee question types */
    'question_textbox_single'       => 1,
    'question_textbox_multi'        => 2,
    'question_dropdown_single'      => 3,
    'question_dropdown_multi'       => 4,
    'question_checkbox_multi'       => 5,
    'question_radio_single'         => 6,


    'default_timezone'           => 30, #Europe/Dublin
    'default_currency'           => 2, #Euro
    'default_date_format'        => 'j M, Y',
    'default_date_picker_format' => 'd M, yyyy',
    'default_datetime_format'    => 'F j, Y, g:i a',
    'default_query_cache'        => 120, #Minutes
    'default_locale'             => 'en',
    'default_payment_gateway'    => 1, #Stripe=1 Paypal=2 BitPay=3 MIGS=4

    'cdn_url_user_assets'   => '',
    'cdn_url_static_assets' => ''
];
