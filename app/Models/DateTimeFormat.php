<?php namespace App\Models;

/*
  Attendize.com   - Event Management & Ticketing
 */

/**
 * Description of DateTimeFormat
 *
 * @author Dave
 */
class DateTimeFormat extends \Illuminate\Database\Eloquent\Model {
    
    protected $table = 'datetime_formats';
    
    public $timestamps = false;
	protected $softDelete = false;
}
