<?php

namespace App\Models;

use App\Attendize\Utils;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends MyBaseModel
{
    use SoftDeletes;

    protected $rules = [
        'first_name' => ['required'],
        'last_name'  => ['required'],
        'email'      => ['required', 'email'],
    ];

    protected $messages = [];

    public function users()
    {
        return $this->hasMany('\App\Models\User');
    }

    public function orders()
    {
        return $this->hasMany('\App\Models\Order');
    }

    public function currency()
    {
        return $this->hasOne('\App\Models\Currency');
    }

    public function getStripeApiKeyAttribute()
    {
        if (Utils::isAttendize()) {
            return $this->stripe_access_token;
        }

        return $this->stripe_secret_key;
    }
}
