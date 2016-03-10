<?php

namespace App\Http\Controllers;

use App\Models\Event;
use File;
use Image;
use Input;
use Response;
use Validator;
use View;
use Illuminate\Http\Request;

class EventCustomizeController extends MyBaseController
{
    public function showCustomize($event_id = '', $tab = '')
    {
        $data = $this->getEventViewData($event_id, [
            'available_bg_images' => $this->getAvailableBackgroundImages(),
            'available_bg_images_thumbs' => $this->getAvailableBackgroundImagesThumbs(),
            'tab' => $tab,
        ]);

        return View::make('ManageEvent.Customize', $data);
    }

    public function getAvailableBackgroundImages()
    {
        $images = [];

        $files = File::files(public_path() . '/' . config('attendize.event_bg_images'));

        foreach ($files as $image) {
            $images[] = str_replace(public_path(), '', $image);
        }

        return $images;
    }

    public function getAvailableBackgroundImagesThumbs()
    {
        $images = [];

        $files = File::files(public_path() . '/' . config('attendize.event_bg_images') . '/thumbs');

        foreach ($files as $image) {
            $images[] = str_replace(public_path(), '', $image);
        }

        return $images;
    }


    public function postEditEventTicketSocial($event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        $rules = [
            'social_share_text' => ['max:3000'],
            'social_show_facebook' => ['boolean'],
            'social_show_twitter' => ['boolean'],
            'social_show_linkedin' => ['boolean'],
            'social_show_email' => ['boolean'],
            'social_show_googleplus' => ['boolean'],
        ];

        $messages = [
            'social_share_text.max' => 'Please keep the shate text under 3000 characters.',
        ];

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $event->social_share_text = Input::get('social_share_text');
        $event->social_show_facebook = Input::get('social_show_facebook');
        $event->social_show_linkedin = Input::get('social_show_linkedin');
        $event->social_show_twitter = Input::get('social_show_twitter');
        $event->social_show_email = Input::get('social_show_email');
        $event->social_show_googleplus = Input::get('social_show_googleplus');
        $event->save();

        return Response::json([
            'status' => 'success',
            'message' => 'Social Settings Succesfully Upated',
        ]);

    }

    /**
     * Update ticket details
     *
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function postEditEventTicketDesign(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        $rules = [
            //'barcode_type' => ['required'],
            'ticket_border_color' => ['required'],
            'ticket_bg_color' => ['required'],
            'ticket_text_color' => ['required'],
            'ticket_sub_text_color' => ['required'],
        ];
        $messages = [
            'ticket_bg_color.required' => 'Please enter a background color.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $event->barcode_type = $request->get('barcode_type');
        $event->ticket_border_color = $request->get('ticket_border_color');
        $event->ticket_bg_color = $request->get('ticket_bg_color');
        $event->ticket_text_color = $request->get('ticket_text_color');
        $event->ticket_sub_text_color = $request->get('ticket_sub_text_color');

        $event->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket Settings Updated',
        ]);
    }

    public function postEditEventFees($event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        $rules = [
            'organiser_fee_percentage' => ['numeric', 'between:0,100'],
            'organiser_fee_fixed' => ['numeric', 'between:0,100'],
        ];
        $messages = [
            'organiser_fee_percentage.numeric' => 'Please enter a value between 0 and 100',
            'organiser_fee_fixed.numeric' => 'Please check the format. It shoud be in the format 0.00.',
            'organiser_fee_fixed.between' => 'Please enter a value between 0 and 100.',
        ];

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $event->organiser_fee_percentage = Input::get('organiser_fee_percentage');
        $event->organiser_fee_fixed = Input::get('organiser_fee_fixed');
        $event->save();

        return Response::json([
            'status' => 'success',
            'message' => 'Order Page Succesfully Upated',
        ]);
    }

    public function postEditEventOrderPage($event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        // Just plain text so no validation needed (hopefully)
        $rules = [];
        $messages = [];

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $event->pre_order_display_message = trim(Input::get('pre_order_display_message'));
        $event->post_order_display_message = trim(Input::get('post_order_display_message'));
        $event->ask_for_all_attendees_info = (Input::get('ask_for_all_attendees_info') == 'on');
        $event->save();

        return Response::json([
            'status' => 'success',
            'message' => 'Order Page Successfully Upated',
        ]);
    }

    public function postEditEventDesign($event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        $rules = [
            'bg_image_path' => ['mimes:jpeg,jpg,png', 'max:4000'],
        ];
        $messages = [
            'bg_image_path.mimes' => 'Please ensure you are uploading an image (JPG, PNG, JPEG)',
            'bg_image_path.max' => 'Please ensure the image is not larger than 2.5MB',
        ];

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->fails()) {
            return Response::json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        if (Input::get('bg_image_path_custom') && Input::get('bg_type') == 'image') {
            $event->bg_image_path = Input::get('bg_image_path_custom');
            $event->bg_type = 'image';
        }

        if (Input::get('bg_color') && Input::get('bg_type') == 'color') {
            $event->bg_color = Input::get('bg_color');
            $event->bg_type = 'color';
        }

        /*
         * Not in use for now.
         */
        if (Input::hasFile('bg_image_path') && Input::get('bg_type') == 'custom_image') {
            $path = public_path() . '/' . config('attendize.event_images_path');
            $filename = 'event_bg-' . md5($event->id) . '.' . strtolower(Input::file('bg_image_path')->getClientOriginalExtension());

            $file_full_path = $path . '/' . $filename;

            Input::file('bg_image_path')->move($path, $filename);

            $img = Image::make($file_full_path);

            $img->resize(1400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $img->save($file_full_path, 75);

            $event->bg_image_path = config('attendize.event_images_path') . '/' . $filename;
            $event->bg_type = 'custom_image';

            \Storage::put(config('attendize.event_images_path') . '/' . $filename, file_get_contents($file_full_path));
        }

        $event->save();

        return Response::json([
            'status' => 'success',
            'message' => 'Event Page Succesfully Upated',
            'runThis' => 'document.getElementById(\'previewIframe\').contentWindow.location.reload(true);',
        ]);
    }
}
