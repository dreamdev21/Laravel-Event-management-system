<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Contracts\Auth\Guard;

class UserLogoutController extends Controller {
    
    protected $auth;
    
    public function __construct(Guard $auth) {
        $this->auth = $auth;
    }


    public function doLogout() {
        $this->auth->logout();
        return \Redirect::to('/?logged_out=yup');
    }
    
}