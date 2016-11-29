<?php

namespace App\Http\Controllers;

use App\Models\Organiser;
use File;
use Image;
use Illuminate\Http\Request;
use Validator;

class OrganiserCustomizeController extends MyBaseController
{
    /**
     * Show organiser setting page
     *
     * @param $organiser_id
     * @return mixed
     */
    public function showCustomize($organiser_id)
    {
        $data = [
            'organiser' => Organiser::scope()->findOrFail($organiser_id),
        ];

        return view('ManageOrganiser.Customize', $data);
    }

    /**
     * Edits organiser settings / design etc.
     *
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function postEditOrganiser(Request $request, $organiser_id)
    {
        $organiser = Organiser::scope()->find($organiser_id);

        if (!$organiser->validate($request->all())) {
            return response()->json([
                'status'   => 'error',
                'messages' => $organiser->errors(),
            ]);
        }

        $organiser->name                  = $request->get('name');
        $organiser->about                 = $request->get('about');
        $organiser->google_analytics_code = $request->get('google_analytics_code');
        $organiser->email                 = $request->get('email');
        $organiser->enable_organiser_page = $request->get('enable_organiser_page');
        $organiser->facebook              = $request->get('facebook');
        $organiser->twitter               = $request->get('twitter');

        if ($request->get('remove_current_image') == '1') {
            $organiser->logo_path = '';
        }

        if ($request->hasFile('organiser_logo')) {
            $organiser->setLogo($request->file('organiser_logo'));
        }

        $organiser->save();

        session()->flash('message', 'Successfully Updated Organiser');

        return response()->json([
            'status'      => 'success',
            'redirectUrl' => '',
        ]);
    }

    /**
     * Edits organiser profile page colors / design
     *
     * @param Request $request
     * @param $organiser_id
     * @return mixed
     */
    public function postEditOrganiserPageDesign(Request $request, $organiser_id)
    {
        $event = Organiser::scope()->findOrFail($organiser_id);

        $rules = [
            'page_bg_color'        => ['required'],
            'page_header_bg_color' => ['required'],
            'page_text_color'      => ['required'],
        ];
        $messages = [
            'page_header_bg_color.required' => 'Please enter a header background color.',
            'page_bg_color.required'        => 'Please enter a background color.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->messages()->toArray(),
            ]);
        }

        $event->page_bg_color        = $request->get('page_bg_color');
        $event->page_header_bg_color = $request->get('page_header_bg_color');
        $event->page_text_color      = $request->get('page_text_color');

        $event->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Organiser Design Successfully Updated',
        ]);
    }
}
