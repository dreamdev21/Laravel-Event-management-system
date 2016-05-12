<?php

namespace App\Models;

use Str;

class Organiser extends MyBaseModel
{
    /**
     * The validation rules for the model.
     *
     * @var array $rules
     */
    protected $rules = [
        'name'           => ['required'],
        'email'          => ['required', 'email'],
        'organiser_logo' => ['mimes:jpeg,jpg,png', 'max:10000'],
    ];

    /**
     * The validation error messages for the model.
     *
     * @var array $messages
     */
    protected $messages = [
        'name.required'        => 'You must at least give a name for the event organiser.',
        'organiser_logo.max'   => 'Please upload an image smaller than 10Mb',
        'organiser_logo.size'  => 'Please upload an image smaller than 10Mb',
        'organiser_logo.mimes' => 'Please select a valid image type (jpeg, jpg, png)',
    ];

    /**
     * The account associated with the organiser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('\App\Models\Account');
    }

    /**
     * The events associated with the organizer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany('\App\Models\Event');
    }

    /**
     * The attendees associated with the organizer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function attendees()
    {
        return $this->hasManyThrough('\App\Models\Attendee', '\App\Models\Event');
    }

    /**
     * Get the orders related to an organiser
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function orders()
    {
        return $this->hasManyThrough('\App\Models\Order', '\App\Models\Event');
    }

    /**
     * Get the full logo path of the organizer.
     *
     * @return mixed|string
     */
    public function getFullLogoPathAttribute()
    {
        if ($this->logo_path && (file_exists(config('attendize.cdn_url_user_assets').'/'.$this->logo_path) || file_exists(public_path($this->logo_path)))) {
            return config('attendize.cdn_url_user_assets').'/'.$this->logo_path;
        }

        return config('attendize.fallback_organiser_logo_url');
    }

    /**
     * Get the url of the organizer.
     *
     * @return string
     */
    public function getOrganiserUrlAttribute()
    {
        return route('showOrganiserHome', [
            'organiser_id'   => $this->id,
            'organiser_slug' => Str::slug($this->oraganiser_name),
        ]);
    }

    /**
     * Get the sales volume of the organizer.
     *
     * @return mixed|number
     */
    public function getOrganiserSalesVolumeAttribute()
    {
        return $this->events->sum('sales_volume');
    }

    /**
     * TODO:implement DailyStats method
     */
    public function getDailyStats()
    {
    }
}
