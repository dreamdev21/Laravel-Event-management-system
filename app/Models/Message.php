<?php

namespace App\Models;

/*
  Attendize.com   - Event Management & Ticketing
 */

/**
 * Description of Message.
 *
 * @author Dave
 */
class Message extends MyBaseModel
{
    public function event()
    {
        return $this->belongsTo('\App\Models\Event');
    }

    public function getRecipientsLabelAttribute()
    {
        if ($this->recipients == 0) {
            return 'All Attendees';
        }

        $ticket = Ticket::scope()->find($this->recipients);

        return 'Ticket: '.$ticket->title;
    }

    public function getDates()
    {
        return ['created_at', 'updated_at', 'sent_at'];
    }
}
