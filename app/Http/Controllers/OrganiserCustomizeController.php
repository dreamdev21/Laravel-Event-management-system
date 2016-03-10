<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use File;
use Image;
use Input;
use Response;
use Session;
use View;

class OrganiserCustomizeController extends MyBaseController
{
    public function showCustomize($organiser_id)
    {
        $data = [
            'organiser' => Organiser::scope()->findOrFail($organiser_id),
        ];

        return View::make('ManageOrganiser.Customize', $data);
    }

    public function postEditOrganiser($organiser_id)
    {
        $organiser = Organiser::scope()->find($organiser_id);

        if (!$organiser->validate(Input::all())) {
            return Response::json([
                'status'   => 'error',
                'messages' => $organiser->errors(),
            ]);
        }

        $organiser->name = Input::get('name');
        $organiser->about = Input::get('about');
        $organiser->email = Input::get('email');
        $organiser->facebook = Input::get('facebook');
        $organiser->twitter = Input::get('twitter');

        /*
         * If the email has been changed the user must confirm the email.
         */
        if ($organiser->email !== Input::get('email')) {
            $organiser->is_email_confirmed = 0;
        }

        if (Input::get('remove_current_image') == '1') {
            $organiser->logo_path = '';
        }

        if (Input::hasFile('organiser_logo')) {
            $the_file = \File::get(Input::file('organiser_logo')->getRealPath());
            $file_name = str_slug($organiser->name).'-logo-'.$organiser->id.'.'.strtolower(Input::file('organiser_logo')->getClientOriginalExtension());

            $relative_path_to_file = config('attendize.organiser_images_path').'/'.$file_name;
            $full_path_to_file = public_path($relative_path_to_file);

            $img = Image::make($the_file);

            $img->resize(200, 200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $img->save($full_path_to_file);

            if (\Storage::put($file_name, $the_file)) {
                $organiser->logo_path = $relative_path_to_file;
            }
        }

        $organiser->save();

        Session::flash('message', 'Successfully Updated Organiser');

        return Response::json([
            'status'      => 'success',
            'redirectUrl' => route('showOrganiserCustomize', [
                'organiser_id' => $organiser->id,
            ]),
        ]);
    }
}
