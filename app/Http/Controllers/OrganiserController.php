<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use Illuminate\Http\Request;
use Image;

class OrganiserController extends MyBaseController
{
    /**
     * Show the select organiser page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showSelectOrganiser()
    {
        return view('ManageOrganiser.SelectOrganiser');
    }

    /**
     * Show the create organiser page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showCreateOrganiser()
    {
        return view('ManageOrganiser.CreateOrganiser');
    }

    /**
     * Create the organiser
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCreateOrganiser(Request $request)
    {
        $organiser = Organiser::createNew(false, false, true);

        if (!$organiser->validate($request->all())) {
            return response()->json([
                'status'   => 'error',
                'messages' => $organiser->errors(),
            ]);
        }

        $organiser->name             = $request->get('name');
        $organiser->about            = $request->get('about');
        $organiser->email            = $request->get('email');
        $organiser->facebook         = $request->get('facebook');
        $organiser->twitter          = $request->get('twitter');
        $organiser->confirmation_key = str_random(15);

        if ($request->hasFile('organiser_logo')) {
            $path = public_path().'/'.config('attendize.organiser_images_path');
            $filename = 'organiser_logo-'.$organiser->id.'.'.strtolower($request->file('organiser_logo')->getClientOriginalExtension());

            $file_full_path = $path.'/'.$filename;

            $request->file('organiser_logo')->move($path, $filename);

            $img = Image::make($file_full_path);

            $img->resize(250, 250, function ($constraint) {
                $constraint->upsize();
            });

            $img->save($file_full_path);

            if (file_exists($file_full_path)) {
                $organiser->logo_path = config('attendize.organiser_images_path').'/'.$filename;
            }
        }

        $organiser->save();

        session()->flash('message', 'Successfully Created Organiser.');

        return response()->json([
            'status'      => 'success',
            'message'     => 'Refreshing..',
            'redirectUrl' => route('showOrganiserEvents', [
                'organiser_id' => $organiser->id,
                'first_run'    => 1
            ]),
        ]);
    }
}
