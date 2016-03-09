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

    public $dates = ['deleted_at'];

    protected $messages = [];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'timezone_id',
        'date_format_id',
        'datetime_format_id',
        'currency_id',
        'name',
        'last_ip',
        'last_login_date',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'email_footer',
        'is_active',
        'is_banned',
        'is_beta',
        'stripe_access_token',
        'stripe_refresh_token',
        'stripe_secret_key',
        'stripe_publishable_key',
        'stripe_data_raw'
    ];

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
