<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Request,
    View,
    Auth,
    Input,
    Redirect;
use \Illuminate\Contracts\Auth\Guard;

class UserLoginController extends Controller {

    protected $auth;

    public function __construct(Guard $auth) {
        $this->auth = $auth;
        $this->middleware('guest');
    }

    public function showLogin() {

        /*
         * If there's an ajax request to the login page assume the person has been
         * logged out and redirect them to the login page
         */
        if (Request::ajax()) {
            return Response::json(array(
                        'status' => 'success',
                        'redirectUrl' => route('login')
            ));
        }

        return View::make('Public.LoginAndRegister.Login');
    }

    /**
     * Handle the login
     * 
     * @return void
     */
    public function postLogin() {

        $email = Input::get('email');
        $password = Input::get('password');

        if ($this->auth->attempt(array('email' => $email, 'password' => $password), true)) {
            return Redirect::to(route('showSelectOrganiser'));
        }
        return Redirect::to('login?failed=yup')->with('message', 'Your username/password combination was incorrect')
                        ->withInput();
    }




}
