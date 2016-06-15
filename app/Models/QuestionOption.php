<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionOption extends MyBaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @access protected
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @access public
     * @var bool
     */
    public $timestamps = false;

    /**
     * The question associated with the question option.
     *
     * @access public
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo('\App\Models\Question');
    }
}
