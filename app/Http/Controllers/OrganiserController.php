<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organiser;
use Image;
use Input;
use Response;
use View;

class OrganiserController extends MyBaseController
{
    public function showSelectOragniser()
    {
        return View::make('ManageOrganiser.SelectOrganiser');
    }

    public function showOrganiserDashboard($organiser_id = false)
    {
        $allowed_sorts = ['created_at', 'start_date', 'end_date', 'title'];

        $searchQuery = Input::get('q');
        //$sort_order = Input::get('sort_order') == 'asc' ? 'asc' : 'desc';
        $sort_by = (in_array(Input::get('sort_by'), $allowed_sorts) ? Input::get('sort_by') : 'start_date');

        $events = $searchQuery
                ? Event::scope()->where('title', 'like', '%'.$searchQuery.'%')->orderBy($sort_by, 'desc')->where('organiser_id', '=', $organiser_id)->paginate(12)
                : Event::scope()->where('organiser_id', '=', $organiser_id)->orderBy($sort_by, 'desc')->paginate(12);

        $data = [
            'events'            => $events,
            'organisers'        => Organiser::scope()->orderBy('name')->get(),
            'current_organiser' => Organiser::scope()->find($organiser_id),
            'q'                 => $searchQuery ? $searchQuery : '', //Redundant
            'search'            => [
                'q'        => $searchQuery ? $searchQuery : '',
                'sort_by'  => $sort_by,
                'showPast' => Input::get('past'),
            ],
        ];

        return View::make('ManageEvents.OrganiserDashboard', $data);
    }

    public function showEditOrganiser($organiser_id)
    {
        $organiser = Organiser::scope()->findOrfail($organiser_id);

        return View::make('ManageEvents.Modals.EditOrganiser', [
                    'modal_id'  => Input::get('modal_id'),
                    'organiser' => $organiser,
        ]);
    }

    public function showCreateOrganiser()
    {
        return View::make('ManageOrganiser.CreateOrganiser');

        return View::make('ManageEvents.Modals.CreateOrganiser');
    }

    public function postCreateOrganiser()
    {
        $organiser = Organiser::createNew(false, false, true);

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
        $organiser->confirmation_key = md5(time().rand(0, 999999));

        if (Input::hasFile('organiser_logo')) {
            $path = public_path().'/'.config('attendize.organiser_images_path');
            $filename = 'organiser_logo-'.$organiser->id.'.'.strtolower(Input::file('organiser_logo')->getClientOriginalExtension());

            $file_full_path = $path.'/'.$filename;

            Input::file('organiser_logo')->move($path, $filename);

            $img = Image::make($file_full_path);

            $img->resize(250, 250, function ($constraint) {
                //$constraint->aspectRatio();
                $constraint->upsize();
            });

            $img->save($file_full_path);

            if (file_exists($file_full_path)) {
                $organiser->logo_path = config('attendize.organiser_images_path').'/'.$filename;
            }
        }
        $organiser->save();

        \Session::flash('message', 'Successfully Created Organiser');

        return Response::json([
                    'status'      => 'success',
                    'message'     => 'Refreshing..',
                    'redirectUrl' => route('showOrganiserDashboard', [
                        'organiser_id' => $organiser->id,
                    ]),
        ]);
    }
}
