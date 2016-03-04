<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use PDF;
use File;

class Order extends MyBaseModel {

    use SoftDeletes;

    public $rules = [
        'order_first_name' => ['required'],
        'order_last_name' => ['required'],
        'order_email' => ['required', 'email'],
    ];
    public $messages = [
        'order_first_name.required' => 'Please enter a valid first name',
        'order_last_name.required' => 'Please enter a valid last name',
        'order_email.email' => 'Please enter a valid email',
    ];

    public function orderItems() {
        return $this->hasMany('\App\Models\OrderItem');
    }

    public function attendees() {
        return $this->hasMany('\App\Models\Attendee');
    }

    public function account() {
        return $this->belongsTo('\App\Models\Account');
    }

    public function event() {
        return $this->belongsTo('\App\Models\Event');
    }

    public function tickets() {
        return $this->hasMany('\App\Models\Ticket');
    }

    public function orderStatus() {
        return $this->belongsTo('\App\Models\OrderStatus');
    }

    public function getOrganiserAmountAttribute() {
        return $this->amount + $this->organiser_booking_fee;
    }

    public function getTotalAmountAttribute() {
        return $this->amount + $this->organiser_booking_fee + $this->booking_fee;
    }

    public function getFullNameAttribute() {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Generate and save the PDF tickets 
     * 
     * @todo Move this from the order model
     * @return boolean
     */
    public function generatePdfTickets() {

        $data = [
            'order' => $this,
            'event' => $this->event,
            'tickets' => $this->event->tickets,
            'attendees' => $this->attendees
        ];

        $pdf_file_path = public_path(config('attendize.event_pdf_tickets_path')) . '/' . $this->order_reference;
        $pdf_file      = $pdf_file_path.'.pdf';
        
        if (file_exists($pdf_file)) {
            return true;
        }
        
        if(!is_dir($pdf_file_path)) {
            File::makeDirectory($pdf_file_path, 0777, true, true);
        }

        PDF::setOutputMode('F'); // force to file
        PDF::html('Public.ViewEvent.Partials.PDFTicket', $data, $pdf_file_path);

        $this->ticket_pdf_path = config('attendize.event_pdf_tickets_path').'/'.$this->order_reference.'.pdf';
        $this->save();

        return file_exists($pdf_file);
    }

    public static function boot() {
        parent::boot();

        static::creating(function($order) {
            $order->order_reference = strtoupper(str_random(5)) . date('jn');
        });
    }

}
