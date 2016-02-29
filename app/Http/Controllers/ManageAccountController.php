<?php

namespace App\Http\Controllers;

use Input,
    Response,
    View,
    Auth;
use HttpClient;
use App\Models\Account;
use App\Models\Timezone;
use App\Models\Currency;
use App\Models\User;


class ManageAccountController extends MyBaseController {

    public function showEditAccount() {

        $data = [
            'modal_id' => Input::get('modal_id'),
            'account' => Account::find(Auth::user()->account_id),
            'timezones' => Timezone::lists('location', 'id'),
            'currencies' => Currency::lists('title', 'id')
        ];

        return View::make('ManageAccount.Modals.EditAccount', $data);
    }

    public function showStripeReturn() {

        $error_message = "There was an error connecting your Stripe account. Please try again.";
        
        if (Input::get('error') || !Input::get('code')) {
            //BugSnag::notifyError('Error Connecting to Stripe', Input::get('error'));
            \Session::flash('message', $error_message);

            return redirect()->route('showEventsDashboard');
        }

        $request = [
            'url' => 'https://connect.stripe.com/oauth/token',
            'params' => [

                'client_secret' => STRIPE_SECRET_KEY, //sk_test_iXk2Ky0DlhIcTcKMvsDa8iKI',
                'code' => Input::get('code'),
                'grant_type' => 'authorization_code'
            ]
        ];

        $response = HttpClient::post($request);

        $content = $response->json();
        
        if(isset($content->error) || !isset($content->access_token)) {
            //BugSnag::notifyError('Error Connecting to Stripe', Input::get('error'));
            \Session::flash('message', $error_message);

            return redirect()->route('showEventsDashboard');
        }
        
        $account = Account::find(\Auth::user()->account_id);
        $account->stripe_access_token = $content->access_token;
        $account->stripe_refresh_token = $content->refresh_token;
        $account->stripe_publishable_key = $content->stripe_publishable_key;
        $account->stripe_data_raw = json_encode($content);
        $account->save();

        \Session::flash('message', "You have successfully connected your Stripe account.");
        return redirect()->route('showEventsDashboard');
    }

    public function postEditAccount() {
        $account = Account::find(Auth::user()->account_id);

        if (!$account->validate(Input::all())) {

            return Response::json(array(
                'status' => 'error',
                'messages' => $account->errors()
            ));
        }

        $account->first_name = Input::get('first_name');
        $account->last_name = Input::get('last_name');
        $account->email = Input::get('email');
        $account->timezone_id = Input::get('timezone_id');
        $account->currency_id = Input::get('currency_id');
        $account->save();

        return Response::json(array(
            'status' => 'success',
            'id' => $account->id,
            'message' => 'Account Successfully Updated'
        ));
    }

    public function postEditAccountPayment() {
        $account = Account::find(Auth::user()->account_id);

        $account->stripe_publishable_key = Input::get('stripe_publishable_key');
        $account->stripe_secret_key = Input::get('stripe_secret_key');

        $account->save();

        return Response::json(array(
            'status' => 'success',
            'id' => $account->id,
            'message' => 'Payment Information Successfully Updated'
        ));

    }

    public function postInviteUser() {
         $rules = array(
		'email'  => array('required', 'email', 'unique:users,email,NULL,id,account_id,'.Auth::user()->account_id),
	);
	
	$messages = array(
		'email.email' => 'Please enter a valid E-mail address.',
		'email.required' => 'E-mail address is required.',
		'email.unique' => 'E-mail already in use for this account.',
	);
	
	$validation = \Validator::make(Input::all(), $rules, $messages);
	
	if ($validation->fails()) {
		return \Response::json([
                    'status' => 'error',
                    'messages'=> $validation->messages()->toArray()
                ]);	
	}
        
        $temp_password = str_random(8);
        
        $user = new User;
        $user->email = Input::get('email');
        $user->password = \Hash::make($temp_password);
        $user->account_id = Auth::user()->account_id;
        $user->save();
        
        $data = [
          'user' => $user,
          'temp_password' => $temp_password,
          'inviter' => Auth::user()
        ];
                        
        \Mail::send('Emails.inviteUser', $data, function($message) use ($data) {
            $message->to($data['user']->email)
                    ->subject($data['inviter']->first_name.' '.$data['inviter']->last_name.' added you to an Attendize Ticketing account.');
        });
        
        return Response::json([
            'status' => 'success',
            'message'=> 'Success! <b>'.$user->email.'</b> has been sent further instructions.' 
        ]);
	
    }
    
}
