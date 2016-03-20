<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;


class AccountPaymentGateway extends MyBaseModel
{

    use softDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_gateway_id',
        'config'
    ];

    public function account() {
        return $this->belongsTo('\App\Models\Account');
    }

    public function payment_gateway()
    {
        return $this->belongsTo('\App\Models\PaymentGateway', 'payment_gateway_id', 'id');
    }





    /**
     * @param $val
     * @return mixed
     */
    public function getConfigAttribute($value) {
        return json_decode($value, true);
    }

    public function setConfigAttribute($value) {
        $this->attributes['config'] = json_encode($value);
    }


}
