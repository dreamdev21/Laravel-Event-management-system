<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/*
  Attendize.com   - Event Management & Ticketing
 */

/**
 * Description of Timezone
 *
 * @author Dave
 */
class Timezone extends \Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
	protected $softDelete = false;
}
