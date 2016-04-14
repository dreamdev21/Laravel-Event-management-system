<?php

namespace app\Http\Controllers\API;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    protected $account_id;

    public function __construct()
    {
        $this->account_id = Auth::guard('api')->user()->account_id;
    }

}