<?php

namespace App\Models;

use Auth,
    Validator;

/*
 * Adapted from: https://github.com/hillelcoren/invoice-ninja/blob/master/app/models/EntityModel.php
 */

class MyBaseModel extends \Illuminate\Database\Eloquent\Model {

    protected $softDelete = true;
    public $timestamps = true;
    protected $rules = array();
    protected $messages = array();
    protected $errors;

    public function validate($data) {
        $v = Validator::make($data, $this->rules, $this->messages);

        if ($v->fails()) {
            $this->errors = $v->messages();
            return false;
        }

        // validation pass
        return true;
    }

    public function errors($returnArray = TRUE) {
        return $returnArray ? $this->errors->toArray() : $this->errors;
    }

    /**
     * 
     * @param int $account_id
     * @param int $user_id
     * @param bool $ignore_user_id
     * @return \className
     */
    public static function createNew($account_id = FALSE, $user_id = FALSE, $ignore_user_id = FALSE) {
        $className = get_called_class();
        $entity = new $className();

        if (Auth::check()) {

            if (!$ignore_user_id) {
                $entity->user_id = Auth::user()->id;
            }

            $entity->account_id = Auth::user()->account_id;
        } else if ($account_id || $user_id) {

            if ($user_id && !$ignore_user_id) {
                $entity->user_id = $user_id;
            }

            $entity->account_id = $account_id;
        } else {
            App::abort(500);
        }

        return $entity;
    }

    public function getFormatedDate($field, $format = 'd-m-Y H:i') {
        return $this->$field === NULL ? NULL : date($format, strtotime($this->$field));
    }

    /**
     * 
     * @param int $accountId
     */
    public function scopeScope($query, $accountId = false) {
        
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

        $query->where(function($query) use ($accountId, $table) {
            $query->whereRaw(\DB::raw('(' . $table . '.account_id = ' . $accountId . ')'));
        });

        return $query;
    }

}
