<?php


/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use Carbon\Carbon;

$factory->define(App\Models\OrderStatus::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->text,
    ];
});

$factory->define(App\Models\TicketStatus::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->text,
    ];
});

$factory->define(App\Models\ReservedTickets::class, function (Faker\Generator $faker) {
    return [
        'ticket_id'         => ,
        'event_id'          =>,
        'quantity_reserved' => 50,
        'expires'           => Carbon::now()->addDays(2),
        'session_id'        => $faker->randomNumber
    ];
});

$factory->define(App\Models\Timezone::class, function (Faker\Generator $faker) {
    return [
        'name'     => 'America/New_York',
        'location' => 'New York'
    ];
});


$factory->define(App\Models\DateFormat::class, function (Faker\Generator $faker) {
    return [
        'format'        => "",//Fill in as desired
        'picker_format' => "",//Fill in as desired
        'label'         => "",//Fill in as desired
    ];
});

$factory->define(App\Models\DateTimeFormat::class, function (Faker\Generator $faker) {
    return [
        'format' => "",//Fill in as desired
        'label'  => "",//Fill in as desired
    ];
});


$factory->define(App\Models\Account::class, function (Faker\Generator $faker) {
    return [
        'first_name'             => $faker->firstName,
        'last_name'              => $faker->lastName,
        'email'                  => $faker->email,
        'timezone_id'            => ,//TIMEZONE FACTORY HERE,
        'date_format_id'         => ,//DATE FACTORY HERE
        'datetime_format_id'     => ,//DATETIME FACTORY HERE
        'currency_id'            => ,//CURRENCY FACTORY HERE
        'name'                   => $faker->name,
        'last_ip'                => "127.0.0.1",
        'last_login_date'        => Carbon::now()->subDays(2),
        'address1'               => $faker->address,
        'address2'               => "",
        'city'                   => $faker->city,
        'state'                  => $faker->stateAbbr,
        'postal_code'            => $faker->postcode,
        'country_id'             => ,//COUNTRY FACTORY HERE
        'email_footer'           => 'Email footer text',
        'is_active'              => false,
        'is_banned'              => false,
        'is_beta'                => false,
        'stripe_access_token'    => str_random(10),
        'stripe_refresh_token'   => str_random(10),
        'stripe_secret_key'      => str_random(10),
        'stripe_publishable_key' => str_random(10),
        'stripe_raw_data'        => $faker->text,

    ];
});

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'account_id'        => $faker->randomDigit,
        'first_name'        => $faker->firstName,
        'last_name'         => $faker->lastName,
        'phone'             => $faker->phoneNumber,
        'email'             => $faker->email,
        'password'          => $faker->password,
        'confirmation_code' => $faker->randomNumber,
        'is_registered'     => false,
        'is_confirmed'      => false,
        'is_parent'         => false,
        'remember_token'    => $faker->randomNumber
    ];
});

$factory->define(App\Models\Organiser::class, function (Faker\Generator $faker) {
    return [
        'account_id'         => factory(App\Models\Account::class)->create()->id,
        'name'               => $faker->name,
        'about'              => $faker->text,
        'email'              => $faker->email,
        'phone'              => $faker->phoneNumber,
        'facebook'           => 'https://facebook.com/organizer-profile',
        'twitter'            => 'https://twitter.com/organizer-profile',
        'logo_path'          => 'path/to/logo',
        'is_email_confirmed' => 0,
    ];
});


//Events Next

$factory->define(App\Models\Order::class, function (Faker\Generator $faker) {
    return [
        'account_id'            =>, //ACCOUNT FACTORY HERE
        'order_status_id'       => factory(App\Models\OrderStatus::class)->create()->id,
        'first_name'            => $faker->firstName,
        'last_name'             => $faker->lastName,
        'email'                 => $faker->email,
        'ticket_pdf_path'       => '/ticket/pdf/path',
        'order_reference'       => $faker->text,
        'transaction_id'        => $faker->text,
        'discount'              => .20,
        'booking_fee'           => .10,
        'organiser_booking_fee' => .10,
        'order_date'            => Carbon::now(),
        'notes'                 => $faker->text,
        'is_deleted'            => 0,
        'is_cancelled'          => 0,
        'is_partially_refunded' => 0,
        'is_refunded'           => 0,
        'amount'                => 20.00,
        'amount_refunded'       => 0,
        'event_id'              => ,//EVENT FACTORY HERE,
   ];
});


$factory->define(App\Models\Ticket::class, function (Faker\Generator $faker) {
    return [
        'edited_by_user_id'    => factory(App\Models\User::class)->create()->id,
        'account_id'           => ,//ACCOUNT FACTORY HERE
       'order_id'              => ,//ORDER FACTORY HERE
       'event_id'              => ,//EVENT FACTORY HERE,
       'title'                 => $faker->name,
       'description'           => $faker->text,
       'price'                 => 50.00,
       'max_per_person'        => 4,
       'min_per_person'        => 1,
       'quantity_available'    => 50,
       'quantity_sold'         => 0,
       'start_sale_date'       => Carbon::now(),
       'end_sale_date'         => Carbon::now()->addDays(20),
       'sales_volume'          => 0,
       'organizer_fees_volume' => 0,
       'is_paused'             => 0
   ];
});

$factory->define(App\Models\OrderItem::class, function (Faker\Generator $faker) {
    return [
        'title'            => $faker->title,
        'quantity'         => 5,
        'unit_price'       => 20.00,
        'unit_booking_fee' => 2.00,
        'order_id'         => , //ORDER FACTORY HERE
   ];
});


$faker->define(App\Models\Message::class, function (Faker\Generator $faker) {
    return [
        'message'    => $faker->text,
        'subject'    => $faker->text,
        'recipients' => 0,

    ];
});


