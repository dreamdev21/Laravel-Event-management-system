<?php

namespace App\Models;

class QuestionAnswer extends MyBaseModel
{

    protected $fillable = [
        'question_id',
        'event_id',
        'attendee_id',
        'account_id',
        'answer_text',
    ];

    public function event()
    {
        return $this->belongsToMany('\App\Models\Event');
    }

    public function attendee()
    {
        return $this->belongsToMany('\App\Models\Attendee');
    }

    public function question()
    {
        return $this->belongsToMany('\App\Models\Question');
    }
}
