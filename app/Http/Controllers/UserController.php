<?php

namespace App\Http\Controllers;

use Auth;
use Hash;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
    /**
     * Show the edit user modal
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showEditUser()
    {
        $data = [
            'user' => Auth::user(),
        ];

        return view('ManageUser.Modals.EditUser', $data);
    }

    /**
     * Updates the current user
     *
     * @param Request $request
     * @return mixed
     */
    public function postEditUser(Request $request)
    {
        $rules = [
            'email'        => [
                'required',
                'email',
                'unique:users,email,' . Auth::user()->id . ',id,account_id,' . Auth::user()->account_id
            ],
            'new_password' => ['min:5', 'confirmed', 'required_with:password'],
            'password'     => 'passcheck',
            'first_name'   => ['required'],
            'last_name'    => ['required'],
        ];

        $messages = [
            'email.email'         => trans('controllermessages.enter-valid-email'),
            'email.required'      => trans('controllermessages.email-required'),
            'password.passcheck'  => trans('controllermessages.password-incorrect'),
            'email.unique'        => trans('controllermessages.email-already-use'),
            'first_name.required' => trans('controllermessages.enter-firstname'),
            'last_name.required'  => trans('controllermessages.enter-lastname'),
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validation->messages()->toArray(),
            ]);
        }

        $user = Auth::user();

        if ($request->get('password')) {
            $user->password = Hash::make($request->get('new_password'));
        }

        $user->first_name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->email = $request->get('email');

        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => trans('controllermessages.successfully-saved-details'),
        ]);
    }
}
