<?php namespace App\Models;

/*
  Attendize.com   - Event Management & Ticketing
 */

class Affiliate extends \Illuminate\Database\Eloquent\Model {
    protected $fillable = array('name', 'visits', 'tickets_sold', 'event_id', 'account_id', 'sales_volume');
    
    public function getDates() {
        return array('created_at', 'updated_at');
    }
}
