<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventImage;
use App\Models\Organiser;
use Auth;
use Carbon\Carbon;
use Image;
use Input;
use Response;
use Validator;
use View;

class EventController extends MyBaseController
{
    public function showCreateEvent()
    {
        $data = [
            'modal_id'     => Input::get('modal_id'),
            'organisers'   => Organiser::scope()->lists('name', 'id'),
            'organiser_id' => Input::get('organiser_id') ? Input::get('organiser_id') : false,
        ];

        return View::make('ManageOrganiser.Modals.CreateEvent', $data);
    }

    public function postCreateEvent()
    {
        $event = Event::createNew();

        if (!$event->validate(Input::all())) {
            return Response::json([
                        'status'   => 'error',
                        'messages' => $event->errors(),
            ]);
        }

        $event->title = Input::get('title');
        $event->description = strip_tags(Input::get('description'));
        $event->start_date = Input::get('start_date') ? Carbon::createFromFormat('d-m-Y H:i', Input::get('start_date')) : null;

        /*
         * Venue location info (Usually autofilled from google maps)
         */

        $is_auto_address = (trim(Input::get('place_id')) !== '');

        if ($is_auto_address) { /* Google auto filled */
            $event->venue_name = Input::get('name');
            $event->venue_name_full = Input::get('venue_name_full');
            $event->location_lat = Input::get('lat');
            $event->location_long = Input::get('lng');
            $event->location_address = Input::get('formatted_address');
            $event->location_country = Input::get('country');
            $event->location_country_code = Input::get('country_short');
            $event->location_state = Input::get('administrative_area_level_1');
            $event->location_address_line_1 = Input::get('route');
            $event->location_address_line_2 = Input::get('locality');
            $event->location_post_code = Input::get('postal_code');
            $event->location_street_number = Input::get('street_number');
            $event->location_google_place_id = Input::get('place_id');
            $event->location_is_manual = 0;
        } else { /* Manually entered */
            $event->venue_name = Input::get('location_venue_name');
            $event->location_address_line_1 = Input::get('location_address_line_1');
            $event->location_address_line_2 = Input::get('location_address_line_2');
            $event->location_state = Input::get('location_state');
            $event->location_post_code = Input::get('location_post_code');
            $event->location_is_manual = 1;
        }

        $event->end_date = Input::get('end_date') ? Carbon::createFromFormat('d-m-Y H:i', Input::get('end_date')) : null;

        $event->currency_id = Auth::user()->account->currency_id;
        //$event->timezone_id = Auth::user()->account->timezone_id;

        if (Input::get('organiser_name')) {
            $organiser = Organiser::createNew(false, false, true);

            $rules = [
                'organiser_name'  => ['required'],
                'organiser_email' => ['required', 'email'],
            ];
            $messages = [
                'organiser_name.required' => 'You must give a name for the event organiser.',
            ];

            $validator = Validator::make(Input::all(), $rules, $messages);

            if ($validator->fails()) {
                return Response::json([
                            'status'   => 'error',
                            'messages' => $validator->messages()->toArray(),
                ]);
            }

            $organiser->name = Input::get('organiser_name');
            $organiser->about = Input::get('organiser_about');
            $organiser->email = Input::get('organiser_email');
            $organiser->facebook = Input::get('organiser_facebook');
            $organiser->twitter = Input::get('organiser_twitter');
            $organiser->save();
            $event->organiser_id = $organiser->id;
        } elseif (Input::get('organiser_id')) {
            $event->organiser_id = Input::get('organiser_id');
        } else { /* Somethings gone horribly wrong */
        }

        $event->save();

        if (Input::hasFile('event_image')) {
            $path = public_path().'/'.config('attendize.event_images_path');
            $filename = 'event_image-'.md5(time().$event->id).'.'.strtolower(Input::file('event_image')->getClientOriginalExtension());

            $file_full_path = $path.'/'.$filename;

            Input::file('event_image')->move($path, $filename);

            $img = Image::make($file_full_path);

            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $img->save($file_full_path);

            /* Upload to s3 */
            \Storage::put(config('attendize.event_images_path').'/'.$filename, file_get_contents($file_full_path));

            $eventImage = EventImage::createNew();
            $eventImage->image_path = config('attendize.event_images_path').'/'.$filename;
            $eventImage->event_id = $event->id;
            $eventImage->save();
        }

        return Response::json([
                    'status'      => 'success',
                    'id'          => $event->id,
                    'redirectUrl' => route('showEventTickets', [
                        'event_id'  => $event->id,
                        'first_run' => 'yup',
                    ]),
        ]);
    }

    public function postEditEvent($event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        if (!$event->validate(Input::all())) {
            return Response::json([
                        'status'   => 'error',
                        'messages' => $event->errors(),
            ]);
        }

        $event->is_live = Input::get('is_live');
        $event->title = Input::get('title');
        $event->description = strip_tags(Input::get('description'));
        $event->start_date = Input::get('start_date') ? Carbon::createFromFormat('d-m-Y H:i', Input::get('start_date')) : null;

        /*
         * If the google place ID is the same as before then don't update the venue
         */
        if ((Input::get('place_id') !== $event->location_google_place_id) || $event->location_google_place_id == '') {
            $is_auto_address = (trim(Input::get('place_id')) !== '');

            if ($is_auto_address) { /* Google auto filled */
                $event->venue_name = Input::get('name');
                $event->venue_name_full = Input::get('venue_name_full');
                $event->location_lat = Input::get('lat');
                $event->location_long = Input::get('lng');
                $event->location_address = Input::get('formatted_address');
                $event->location_country = Input::get('country');
                $event->location_country_code = Input::get('country_short');
                $event->location_state = Input::get('administrative_area_level_1');
                $event->location_address_line_1 = Input::get('route');
                $event->location_address_line_2 = Input::get('locality');
                $event->location_post_code = Input::get('postal_code');
                $event->location_street_number = Input::get('street_number');
                $event->location_google_place_id = Input::get('place_id');
                $event->location_is_manual = 0;
            } else { /* Manually entered */
                $event->venue_name = Input::get('location_venue_name');
                $event->location_address_line_1 = Input::get('location_address_line_1');
                $event->location_address_line_2 = Input::get('location_address_line_2');
                $event->location_state = Input::get('location_state');
                $event->location_post_code = Input::get('location_post_code');
                $event->location_is_manual = 1;
                $event->location_google_place_id = '';
                $event->venue_name_full = '';
                $event->location_lat = '';
                $event->location_long = '';
                $event->location_address = '';
                $event->location_country = '';
                $event->location_country_code = '';
                $event->location_street_number = '';
            }
        }

        $event->end_date = Input::get('end_date') ? Carbon::createFromFormat('d-m-Y H:i', Input::get('end_date')) : null;

        if (Input::get('remove_current_image') == '1') {
            EventImage::where('event_id', '=', $event->id)->delete();
        }

        $event->save();

        if (Input::hasFile('event_image')) {
            $path = public_path().'/'.config('attendize.event_images_path');
            $filename = 'event_image-'.md5(time().$event->id).'.'.strtolower(Input::file('event_image')->getClientOriginalExtension());

            $file_full_path = $path.'/'.$filename;

            Input::file('event_image')->move($path, $filename);

            $img = Image::make($file_full_path);

            $img->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $img->save($file_full_path);

            \Storage::put(config('attendize.event_images_path').'/'.$filename, file_get_contents($file_full_path));

            EventImage::where('event_id', '=', $event->id)->delete();

            $eventImage = EventImage::createNew();
            $eventImage->image_path = config('attendize.event_images_path').'/'.$filename;
            $eventImage->event_id = $event->id;
            $eventImage->save();
        }

        return Response::json([
                    'status'      => 'success',
                    'id'          => $event->id,
                    'message'     => 'Event Successfully Updated',
                    'redirectUrl' => '',
        ]);
    }

    public function postUploadEventImage()
    {
        if (Input::hasFile('event_image')) {
            $the_file = \File::get(Input::file('event_image')->getRealPath());
            $file_name = 'event_details_image-'.md5(microtime()).'.'.strtolower(Input::file('event_image')->getClientOriginalExtension());

            $relative_path_to_file = config('attendize.event_images_path').'/'.$file_name;
            $full_path_to_file = public_path().'/'.$relative_path_to_file;

            $img = Image::make($the_file);

            $img->resize(1000, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $img->save($full_path_to_file);
            if (\Storage::put($file_name, $the_file)) {
                return Response::json([
                    'link' => '/'.$relative_path_to_file,
                ]);
            }

            return Response::json([
                    'error' => 'There was a problem uploading your image.',
                ]);
        }
    }
}
