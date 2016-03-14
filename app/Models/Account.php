<?php

namespace App\Models;

use App\Attendize\Utils;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends MyBaseModel
{
    use SoftDeletes;

    /**
     * The validation rules
     *
     * @var array $rules
     */
    protected $rules = [
        'first_name' => ['required'],
        'last_name'  => ['required'],
        'email'      => ['required', 'email'],
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array $dates
     */
    public $dates = ['deleted_at'];

    /**
     * The validation error messages.
     *
     * @var array $messages
     */
    protected $messages = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array $fillable
     */
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

    /**
     * The users associated with the account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('\App\Models\User');
    }

    /**
     * The orders associated with the account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('\App\Models\Order');
    }

    /**
     * The currency associated with the account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currency()
    {
        return $this->hasOne('\App\Models\Currency');
    }

    /**
     * Get the stripe api key.
     *
     * @return \Illuminate\Support\Collection|mixed|static
     */
    public function getStripeApiKeyAttribute()
    {
        if (Utils::isAttendize()) {
            return $this->stripe_access_token;
        }

        return $this->stripe_secret_key;
    }
}
