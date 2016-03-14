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

    /**
     * The events associated with the question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function events()
    {
        return $this->belongsToMany('\App\Models\Event');
    }

    /**
     * The type associated with the question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function question_types()
    {
        return $this->hasOne('\App\Models\QuestionType');
    }
}
