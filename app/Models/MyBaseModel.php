<?php

namespace App\Models;

use Auth;
use Validator;

/*
 * Adapted from: https://github.com/hillelcoren/invoice-ninja/blob/master/app/models/EntityModel.php
 */

class MyBaseModel extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool $timestamps
     */
    public $timestamps = true;
    /**
     * Indicates whether the model uses soft deletes.
     *
     * @var bool $softDelete
     */
    protected $softDelete = true;
    /**
     * The validation rules of the model.
     *
     * @var array $rules
     */
    protected $rules = [];

    /**
     * The validation error messages of the model.
     *
     * @var array $messages
     */
    protected $messages = [];

    /**
     * The validation errors of model.
     *
     * @var  $errors
     */
    protected $errors;

    /**
     * Create a new model.
     *
     * @param int $account_id
     * @param int $user_id
     * @param bool $ignore_user_id
     *
     * @return \className
     */
    public static function createNew($account_id = false, $user_id = false, $ignore_user_id = false)
    {
        $className = get_called_class();
        $entity = new $className();

        if (Auth::check()) {
            if (!$ignore_user_id) {
                $entity->user_id = Auth::user()->id;
            }

            $entity->account_id = Auth::user()->account_id;
        } elseif ($account_id || $user_id) {
            if ($user_id && !$ignore_user_id) {
                $entity->user_id = $user_id;
            }

            $entity->account_id = $account_id;
        } else {
            App::abort(500);
        }

        return $entity;
    }

    /**
     * Validate the model instance.
     *
     * @param $data
     *
     * @return bool
     */
    public function validate($data)
    {
        $v = Validator::make($data, $this->rules, $this->messages);

        if ($v->fails()) {
            $this->errors = $v->messages();

            return false;
        }

        // validation pass
        return true;
    }

    /**
     * Gets the validation error messages.
     *
     * @param bool $returnArray
     *
     * @return mixed
     */
    public function errors($returnArray = true)
    {
        return $returnArray ? $this->errors->toArray() : $this->errors;
    }

    /**
     * Get a formatted date.
     *
     * @param        $field
     * @param string $format
     *
     * @return bool|null|string
     */
    public function getFormattedDate($field, $format = 'd-m-Y H:i')
    {
        return $this->$field === null ? null : date($format, strtotime($this->$field));
    }

    /**
     * Ensures each query looks for account_id
     *
     * @param $query
     * @param bool $accountId
     * @return mixed
     */
    public function scopeScope($query, $accountId = false)
    {

        /*
         * GOD MODE - DON'T UNCOMMENT!
         * returning $query before adding the account_id condition will let you
         * browse all events etc. in the system.
         * //return  $query;
         */

        if (!$accountId) {
            $accountId = Auth::user()->account_id;
        }

        $table = $this->getTable();

        $query->where(function ($query) use ($accountId, $table) {
            $query->whereRaw(\DB::raw('(' . $table . '.account_id = ' . $accountId . ')'));
        });

        return $query;
    }
}
