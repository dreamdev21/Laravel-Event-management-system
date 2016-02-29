<?php

namespace App\Http\Controllers;

use Input,
    Response,
    Auth,
    Validator;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller {

    public function showEditUser() {

        $data = [
            'user' => \Auth::user(),
            'modal_id' => \Input::get('modal_id')
        ];

        return \View::make('ManageUser.Modals.EditUser', $data);
    }

    public function postEditUser() {

        $rules = array(
            'email' => ['required', 'email', 'exists:users,email,account_id,' . Auth::user()->account_id],
            'new_password' => ['min:5', 'confirmed', 'required_with:password'],
            'password' => 'passcheck',
            'first_name' => ['required'],
            'last_name' => ['required']
        );

        $messages = [
            'email.email' => 'Please enter a valid E-mail address.',
            'email.required' => 'E-mail address is required.',
            'password.passcheck' => 'This password is incorrect.',
            'email.exists' => 'This E-mail has is already in use.',
            'first_name.required' => 'Please enter your first name.'
        ];

        $validation = \Validator::make(Input::all(), $rules, $messages);

        if ($validation->fails()) {
            return Response::json([
                        'status' => 'error',
                        'messages' => $validation->messages()->toArray()
            ]);
        }

        $user = Auth::user();

        if (Input::get('password')) {
            $user->password = \Hash::make(Input::get('new_password'));
        }

        $user->first_name = Input::get('first_name');
        $user->last_name = Input::get('last_name');



        //$user->email = Input::get('email');
        $user->save();

        return Response::json([
                    'status' => 'success',
                    'message' => 'Successfully Edited User'
        ]);
    }

}
