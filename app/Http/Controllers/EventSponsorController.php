<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sponsor;
use File;
use Illuminate\Http\Request;
use Image;
use Validator;

class EventSponsorController extends MyBaseController
{
    /* TODO: PROTECT THE SPONSORS FROM DELETION BY UNAUTHORIZED USERS */

    /**
     * Show the Sponsor Overview Page
     *
     * @param int $event_id
     *
     * @return \Illuminate\View\View
     */
    public function showSponsors($event_id)
    {
        $data = $this->getEventViewData($event_id);

        return view('ManageEvent.Sponsors', $data);
    }

    /**
     * Return the Sponsor Create Modal
     *
     * @param $event_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCreateSponsor($event_id)
    {
        return view('ManageEvent.Modals.CreateSponsor', [
            'event' => Event::scope()->find($event_id),
        ]);
    }


    /**
     * Persist a new Sponsor to the Database
     *
     * @param \Illuminate\Http\Request $request
     * @param $event_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postCreateSponsor(Request $request, $event_id)
    {
        $this->persistSponsor($request, null, $event_id);

        return response()->json([
            'status' => 'success',
            'message' => 'The sponsor has been created',
            'redirectUrl' => route('showEventSponsors', [
                'event_id' => $event_id,
            ]),
        ]);
    }


    /**
     * Shows  'Manage Sponsor' modal
     *
     * @param Request $request
     * @param int $event_id
     * @param int $sponsor_id
     *
     * @return mixed
     */
    public function showEditSponsor(Request $request, $event_id, $sponsor_id)
    {
        $data = [
            'sponsor' => Sponsor::find($sponsor_id),
        ];

        return view('ManageEvent.Modals.ManageSponsor', $data);
    }

    /**
     * Persist Sponsor Changes to the Database
     *
     * @param \Illuminate\Http\Request $request
     *
     * @param $event_id
     * @param $sponsor_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEditSponsor(Request $request, $event_id, $sponsor_id)
    {
        $this->persistSponsor($request, $sponsor_id);

        return response()->json([
            'status' => 'success',
            'message' => 'The sponsor has been updated',
            'redirectUrl' => route('showEventSponsors', [
                'event_id' => $event_id,
            ]),
        ]);
    }


    /**
     * Delete a Sponsor
     *
     * @param \Illuminate\Http\Request $request
     *
     * @param $event_id
     * @param $sponsor_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDeleteSponsor(Request $request, $event_id, $sponsor_id)
    {
        Sponsor::where('id', $sponsor_id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'The sponsor has been deleted',
            'redirectUrl' => route('showEventSponsors', [
                'event_id' => $event_id,
            ]),
        ]);
    }

    /**
     * Persist a Sponsor to the Database
     *
     * @param \Illuminate\Http\Request $request
     * @param null|int $sponsor_id
     * @param null|int $event_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function persistSponsor(Request $request, $sponsor_id = null, $event_id = null)
    {
        $rules = [
            'name' => 'required|max:250',
            'sponsor_logo' => 'image',
            'on_ticket' => 'boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        if ($sponsor_id) {
            $sponsor = Sponsor::findOrFail($sponsor_id);
        } else {
            $sponsor = new Sponsor;

            /* TODO: Protect this by checking which events the authenticated users can manage */
            $sponsor->event_id = $event_id;
        }

        if ($request->hasFile('sponsor_logo')) {
            $file = $request->file('sponsor_logo');
            $name = md5_file($file->path()) . md5(time()) . '.' . $file->guessExtension();
            $path = 'user_content/sponsors/';

            $file->move(public_path($path), $name);
            $sponsor->logo_path = $path . $name;
        }

        $sponsor->on_ticket = $request->has('on_ticket');
        $sponsor->name = $request->get('name');

        $sponsor->save();
    }
}