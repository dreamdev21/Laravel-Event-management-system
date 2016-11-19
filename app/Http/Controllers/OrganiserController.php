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
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
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

        $organiser->name = $request->get('name');
        $organiser->about = $request->get('about');
        $organiser->email = $request->get('email');
        $organiser->facebook = $request->get('facebook');
        $organiser->twitter = $request->get('twitter');
        $organiser->confirmation_key = str_random(15);

        if ($request->hasFile('organiser_logo')) {
            $organiser->setLogo($request->file('organiser_logo'));
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
