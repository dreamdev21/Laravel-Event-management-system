<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/*
  Attendize.com   - Event Management & Ticketing
 */

/**
 * Description of Attendees.
 *
 * @author Dave
 */
class Attendee extends MyBaseModel
{
    use SoftDeletes;


    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'event_id',
        'order_id',
        'ticket_id',
        'account_id',
        'reference',
    ];

     /**
      * Generate a private referennce number for the attendee. Use for checking in the attendee.
      */
     public static function boot()
     {
         parent::boot();

         static::creating(function ($order) {
            $order->private_reference_number = str_pad(rand(0, pow(10, 9) - 1), 9, '0', STR_PAD_LEFT);
        });
     }

    public function order()
    {
        return $this->belongsTo('\App\Models\Order');
    }

    public function ticket()
    {
        return $this->belongsTo('\App\Models\Ticket');
    }

    public function event()
    {
        return $this->belongsTo('\App\Models\Event');
    }

    public function scopeWithoutCancelled($query)
    {
        return $query->where('attendees.is_cancelled', '=', 0);
    }

//
//    public function getReferenceAttribute() {
//        return $this->order->order_reference
//    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getDates()
    {
        return ['created_at', 'updated_at', 'arrival_time'];
    }


}
