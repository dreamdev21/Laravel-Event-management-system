<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;


class Ticket extends MyBaseModel {
    use SoftDeletes;
    
    public $rules = [
        'title' => array('required'),
        'price' => array('required', 'numeric', 'min:0'),
        'start_sale_date' => array('date'),
        'end_sale_date' => array('date', 'after:start_sale_date'),
        'quantity_available' => ['integer', 'min:0']
    ];
    public $messages = [
        'price.numeric' => 'The price must be a valid number (e.g 12.50)',
        'title.required' => 'You must at least give a title for your ticket. (e.g Early Bird)',
        'quantity_available.integer' => 'Please ensure the quantity available is a number.'
    ];

    public function event() {
        return $this->belongsTo('\App\Models\Event');
    }

    public function order() {
        return $this->belongsToMany('\App\Models\Order');
    }

    public function questions() {
        return $this->belongsToMany('\App\Models\Question', 'ticket_question');
    }
    
    public function reserved() {
        
    }

    public function scopeSoldOut($query) {
        $query->where('remaining_tickets', '=', 0);
    }

    /*
     * Getters & Setters
     */

    public function getDates() {
        return array('created_at', 'updated_at', 'start_sale_date', 'end_sale_date');
    }

    public function getQuantityRemainingAttribute() {
        
        if(is_null($this->quantity_available)) {
            return 9999; //Better way to do this?
        }
        
        return $this->quantity_available - ($this->quantity_sold + $this->quantity_reserved);
    }
    
    public function getQuantityReservedAttribute() {

        $reserved_total = \DB::table('reserved_tickets')
                ->where('ticket_id', $this->id)
                ->where('expires', '>', \Carbon::now())
                ->sum('quantity_reserved');


        return $reserved_total;
    }
    
    
    public function getBookingFeeAttribute () {
        return (int)ceil($this->price) === 0 ? 0 : round(($this->price * (TICKET_BOOKING_FEE_PERCENTAGE / 100)) + (TICKET_BOOKING_FEE_FIXED), 2);
    }
    
    public function getOrganiserBookingFeeAttribute() {
        return (int)ceil($this->price) === 0 ? 0 : round(($this->price * ($this->event->organiser_fee_percentage / 100)) + ($this->event->organiser_fee_fixed), 2);
    }
    
    public function getTotalBookingFeeAttribute() {
        return $this->getBookingFeeAttribute() + $this->getOrganiserBookingFeeAttribute();
    }
    
    public function getTotalPriceAttribute() {
        return $this->getTotalBookingFeeAttribute() + $this->price;
    }
    
    public function getTicketMaxMinRangAttribute() {
        $range = [];
        
        for($i=$this->min_per_person; $i<=$this->max_per_person; $i++) {
            $range[] = [$i => $i];
        }
        
        return $range;
    }
    

    public function isFree() {
        return (int)ceil($this->price) === 0;
    }
    
    /**
     * Return the maximum figure to go to on dropdowns
     * 
     * @return int

      public function getMaxPerPersonMaxValueAttribute() {
      return $this->max_per_person === -1 ? MAX_TICKETS_PER_PERSON : $this->max_per_person;
      }
     */
    public function getSaleStatusAttribute() {
               
        if ($this->start_sale_date !== NULL) {
            if ($this->start_sale_date->isFuture()) {
                return TICKET_STATUS_BEFORE_SALE_DATE;
            }
        }



        if ($this->end_sale_date !== NULL) {
            if ($this->end_sale_date->isPast()) {
                return TICKET_STATUS_AFTER_SALE_DATE;
            }
        }

        if ((int)$this->quantity_available > 0) {
            if ((int)$this->quantity_remaining <= 0) {
                return TICKET_STATUS_SOLD_OUT;
            }
        }

        if($this->event->start_date->lte(\Carbon::now())) {
            return TICKET_STATUS_OFF_SALE;
        }
        
        return TICKET_STATUS_ON_SALE;
    }
    
    
    
    
    

//    public function setQuantityAvailableAttribute($value) {
//        $this->attributes['quantity_available'] = trim($value) == '' ? -1 : $value;
//    }
//
//    public function setMaxPerPersonAttribute($value) {
//        $this->attributes['max_per_person'] = trim($value) == '' ? -1 : $value;
//    }

}
