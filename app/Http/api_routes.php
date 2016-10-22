<?php

Route::group(['prefix' => 'api', 'middleware' => 'auth:api'], function () {

    /*
     * ---------------
     * Organisers
     * ---------------
     */


    /*
     * ---------------
     * Events
     * ---------------
     */
    Route::resource('events', 'API\EventsApiController');


    /*
     * ---------------
     * Attendees
     * ---------------
     */
    Route::resource('attendees', 'API\AttendeesApiController');


    /*
     * ---------------
     * Orders
     * ---------------
     */

    /*
     * ---------------
     * Users
     * ---------------
     */

    /*
     * ---------------
     * Check-In / Check-Out
     * ---------------
     */


    Route::get('/', function () {
        return response()->json([
            'Hello' => Auth::guard('api')->user()->full_name . '!'
        ]);
    });


});