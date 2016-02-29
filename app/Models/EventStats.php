<?php namespace App\Models;

use DB, Cookie;
use App\Models\Ticket;

class EventStats extends \Illuminate\Database\Eloquent\Model {

    public $timestamps = false;
    public static $unguarded = true;

    
    /**
     * 
     * @todo This shouldn't be in a view.
     * 
     */
    
    /**
     * Update the amount of revenue a ticket has earned
     * 
     * @param int $ticket_id
     * @param float $amount
     * @param bool $deduct
     * @return bool
     */
    public function updateTicketRevenue($ticket_id, $amount, $deduct = FALSE) {
        $ticket = Ticket::find($ticket_id);
        
        if($deduct) {
            $amount = $amount * -1;
        }
        
        $ticket->ticket_revenue = $ticket->ticket_revenue + $amount;
        
        return $ticket->save();
    }
    
    public function updateViewCount($event_id) {

        $stats = $this->firstOrNew([
            'event_id' => $event_id,
            'date' => DB::raw('CURDATE()')
        ]);
        
        $cookie_name = 'visitTrack_'.$event_id.'_'.date('dmy');
        
        if(!Cookie::get($cookie_name)) {
            Cookie::queue($cookie_name, true, 60 * 24 * 14);
            ++$stats->unique_views;
        }
        
        ++$stats->views;

        return $stats->save();
    }
    
    /*
     * TODO: Missing amount?
     */
    public function updateSalesVolume($event_id) {
        $stats = $this->firstOrNew([
            'event_id' => $event_id,
            'date' => DB::raw('CURDATE()')
        ]);
        
        $stats->sales_volume = $stats->sales_volume + $amount;
        
        return $stats->save();
    }


    public function updateTicketsSoldCount($event_id, $count) {

        $stats = $this->firstOrNew([
            'event_id' => $event_id,
            'date' => DB::raw('CURDATE()')
        ]);
        
        $stats->increment('tickets_sold', $count);

        return $stats->save();
    }

}
