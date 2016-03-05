<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Description of Questions.
 *
 * @author Dave
 */
class Question extends MyBaseModel
{
    use SoftDeletes;

    public function events()
    {
        return $this->belongsToMany('\App\Models\Event');
    }

    public function question_types()
    {
        return $this->hasOne('\App\Models\QuestionType');
    }
}
