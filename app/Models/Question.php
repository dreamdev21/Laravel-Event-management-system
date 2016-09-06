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
     * @access public
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function events()
    {
        return $this->belongsToMany('\App\Models\Event');
    }

    /**
     * The type associated with the question.
     *
     * @access public
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function question_type()
    {
        return $this->belongsTo('\App\Models\QuestionType');
    }

    public function answers()
    {
        return $this->hasMany('\App\Models\QuestionAnswer');
    }

    /**
     * The options associated with the question.
     *
     * @access public
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function options()
    {
        return $this->hasMany('\App\Models\QuestionOption');
    }

    public function tickets()
    {
        return $this->belongsToMany('\App\Models\Ticket');
    }

    /**
     * Scope a query to only include active questions.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsEnabled($query)
    {
        return $query->where('is_enabled', 1);
    }
}
