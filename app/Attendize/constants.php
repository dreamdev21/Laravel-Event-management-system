<?php

define('OUTGOING_EMAIL_NOREPLY', env('MAIL_FROM_ADDRESS'));
define('OUTGOING_EMAIL', env('MAIL_FROM_ADDRESS'));
define('OUTGOING_EMAIL_NAME', 'Attendize Event Ticketing');
define('INCOMING_EMAIL', env('MAIL_FROM_ADDRESS'));

define('APP_URL', env('APP_URL'));
define('APP_NAME', 'Attendize Event Ticketing');

define('EVENT_DEFAULT_BG_COLOR', '#B23333');

/* paths */
define('EVENT_IMAGES_PATH', 'user_content/event_images/');
define('ORGANISER_IMAGES_PATH', 'user_content/organiser_images/');
define('EVENT_PDF_TICKETS_PATH', 'user_content/pdf_tickets/');
define('EVENT_BG_IMAGES', 'assets/images/public/EventPage/backgrounds');


/*
 * 
 */
define('FALLBACK_ORGANISER_LOGO_URL', '/assets/images/logo-100x100-lightBg.png');
define('CDN_URL', '');

define('MAX_TICKETS_PER_PERSON', 50);

/* Time in minutes which a user can reserve tickets */
define('CHECKOUT_TIMEOUT_AFTER', 8);

define('TICKET_STATUS_SOLD_OUT', 1);
define('TICKET_STATUS_AFTER_SALE_DATE', 2);
define('TICKET_STATUS_BEFORE_SALE_DATE', 3);
define('TICKET_STATUS_ON_SALE', 4);
define('TICKET_STATUS_OFF_SALE', 5);

/* The fee which we charge users for buying tikets. Fixed fee + % of ticket sale. */
define('TICKET_BOOKING_FEE_FIXED', .0);
define('TICKET_BOOKING_FEE_PERCENTAGE', .0);


define('ORDER_COMPLETE', 1);
define('ORDER_REFUNDED', 2);
define('ORDER_PARTIALLY_REFUNDED', 3);
define('ORDER_CANCELLED', 4);

define('DEFAULT_TIMEZONE', 30); // Europe/Dublin
define('DEFAULT_CURRENCY', 2); // Euro
define('DEFAULT_DATE_FORMAT', 'j M, Y');
define('DEFAULT_DATE_PICKER_FORMAT', 'd M, yyyy');
define('DEFAULT_DATETIME_FORMAT', 'F j, Y, g:i a');
define('DEFAULT_QUERY_CACHE', 120); // minutes
define('DEFAULT_LOCALE', 'en');


define('CDN_URL_USER_ASSETS', '');
define('CDN_URL_STATIC_ASSETS', '');
