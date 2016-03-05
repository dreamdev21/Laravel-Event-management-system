<?php

namespace App\Models;

/*
  Attendize.com   - Event Management & Ticketing
 */

/**
 * Description of Currency.
 *
 * @author Dave
 */
class Currency extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $softDelete = false;

    protected $table = 'currencies';

    public function event()
    {
        return $this->belongsTo('\App\Models\Event');
    }
}
