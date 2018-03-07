<?php

namespace App\Http\Controllers;

use App\Models\Event;
use File;
use Illuminate\Http\Request;
use Image;
use Validator;

class EventCustomizeController extends MyBaseController
{
    /**
     * Show the event customize page
     *
     * @param string $event_id
     * @param string $tab
     * @return \Illuminate\View\View
     */
    public function showCustomize($event_id = '', $tab = '')
    {
        $data = $this->getEventViewData($event_id, [
            'available_bg_images'        => $this->getAvailableBackgroundImages(),
            'available_bg_images_thumbs' => $this->getAvailableBackgroundImagesThumbs(),
            'tab'                        => $tab,
        ]);

        return view('ManageEvent.Customize', $data);
    }

    /**
     * get an array of available event background images
     *
     * @return array
     */
    public function getAvailableBackgroundImages()
    {
        $images = [];

        $files = File::files(public_path() . '/' . config('attendize.event_bg_images'));

        foreach ($files as $image) {
            $images[] = str_replace(public_path(), '', $image);
        }

        return $images;
    }

    /**
     * Get an array of event bg image thumbnails
     *
     * @return array
     */
    public function getAvailableBackgroundImagesThumbs()
    {
        $images = [];

        $files = File::files(public_path() . '/' . config('attendize.event_bg_images') . '/thumbs');

        foreach ($files as $image) {
            $images[] = str_replace(public_path(), '', $image);
        }

        return $images;
    }

    /**
     * Edit social settings of an event
     *
     * @param Request $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEditEventSocial(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        $rules = [
            'social_share_text'      => ['max:3000'],
            'social_show_facebook'   => ['boolean'],
            'social_show_twitter'    => ['boolean'],
            'social_show_linkedin'   => ['boolean'],
            'social_show_email'      => ['boolean'],
            'social_show_googleplus' => ['boolean'],
        ];

        $messages = [
            'social_share_text.max' => trans('controllermessages.keep-under-3000'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $event->social_share_text = $request->get('social_share_text');
        $event->social_show_facebook = $request->get('social_show_facebook');
        $event->social_show_linkedin = $request->get('social_show_linkedin');
        $event->social_show_twitter = $request->get('social_show_twitter');
        $event->social_show_email = $request->get('social_show_email');
        $event->social_show_googleplus = $request->get('social_show_googleplus');
        $event->social_show_whatsapp = $request->get('social_show_whatsapp');
        $event->save();

        return response()->json([
            'status'  => 'success',
            'message' => trans('controllermessages.social-setting-update'),
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
            'ticket_border_color'   => ['required'],
            'ticket_bg_color'       => ['required'],
            'ticket_text_color'     => ['required'],
            'ticket_sub_text_color' => ['required'],
            'is_1d_barcode_enabled' => ['required'],
        ];
        $messages = [
            'ticket_bg_color.required' => trans('controllermessages.enter-background-color'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $event->ticket_border_color = $request->get('ticket_border_color');
        $event->ticket_bg_color = $request->get('ticket_bg_color');
        $event->ticket_text_color = $request->get('ticket_text_color');
        $event->ticket_sub_text_color = $request->get('ticket_sub_text_color');
        $event->is_1d_barcode_enabled = $request->get('is_1d_barcode_enabled');

        $event->save();

        return response()->json([
            'status'  => 'success',
            'message' => trans('controllermessages.ticket-setting-updated'),
        ]);
    }

    /**
     * Edit fees of an event
     *
     * @param Request $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEditEventFees(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        $rules = [
            'organiser_fee_percentage' => ['numeric', 'between:0,100'],
            'organiser_fee_fixed'      => ['numeric', 'between:0,100'],
        ];
        $messages = [
            'organiser_fee_percentage.numeric' => trans('controllermessages.value-0-100'),
            'organiser_fee_fixed.numeric'      => trans('controllermessages.check-format'),
            'organiser_fee_fixed.between'      => trans('controllermessages.value-0-100'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $event->organiser_fee_percentage = $request->get('organiser_fee_percentage');
        $event->organiser_fee_fixed = $request->get('organiser_fee_fixed');
        $event->save();

        return response()->json([
            'status'  => 'success',
            'message' => trans('controllermessages.order-page-updated'),
        ]);
    }

    /**
     * Edit the event order page settings
     *
     * @param Request $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEditEventOrderPage(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        // Just plain text so no validation needed (hopefully)
        $rules = [];
        $messages = [];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $event->pre_order_display_message = trim($request->get('pre_order_display_message'));
        $event->post_order_display_message = trim($request->get('post_order_display_message'));
        $event->offline_payment_instructions = trim($request->get('offline_payment_instructions'));
        $event->enable_offline_payments = (int)$request->get('enable_offline_payments');
        $event->save();

        return response()->json([
            'status'  => 'success',
            'message' => trans('controllermessages.order-page-updated'),
        ]);
    }

    /**
     * Edit event page design/colors etc.
     *
     * @param Request $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function postEditEventDesign(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        $rules = [
            'bg_image_path' => ['mimes:jpeg,jpg,png', 'max:4000'],
        ];
        $messages = [
            'bg_image_path.mimes' => trans('contorllermessages.ensure-uploading-image'),
            'bg_image_path.max'   => trans('contorllermessages.ensure-uploading-image-larger'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status'   => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        if ($request->get('bg_image_path_custom') && $request->get('bg_type') == 'image') {
            $event->bg_image_path = $request->get('bg_image_path_custom');
            $event->bg_type = 'image';
        }

        if ($request->get('bg_color') && $request->get('bg_type') == 'color') {
            $event->bg_color = $request->get('bg_color');
            $event->bg_type = 'color';
        }

        /*
         * Not in use for now.
         */
        if ($request->hasFile('bg_image_path') && $request->get('bg_type') == 'custom_image') {
            $path = public_path() . '/' . config('attendize.event_images_path');
            $filename = 'event_bg-' . md5($event->id) . '.' . strtolower($request->file('bg_image_path')->getClientOriginalExtension());

            $file_full_path = $path . '/' . $filename;

            $request->file('bg_image_path')->move($path, $filename);

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

        return response()->json([
            'status'  => 'success',
            'message' => trans('controllermessages.event-page-updated'),
            'runThis' => 'document.getElementById(\'previewIframe\').contentWindow.location.reload(true);',
        ]);
    }
}
