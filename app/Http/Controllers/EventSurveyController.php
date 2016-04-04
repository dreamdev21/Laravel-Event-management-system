<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventQuestionRequest;
use App\Models\Event;
use App\Models\Question;
use App\Models\QuestionType;
use Illuminate\Http\Request;

/*
  Attendize.com   - Event Management & Ticketing
 */

class EventSurveyController extends MyBaseController
{


    public function showEventSurveys(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        $data = [
            'event' => $event,
            'questions' => $event->questions,
            'sort_order' => 'asc',
            'sort_by' => 'title',
            'q' => '',
        ];

        return view('ManageEvent.Surveys', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showCreateEventQuestion(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        return view('ManageEvent.Modals.CreateQuestion', [
            'event' => $event,
            'modal_id' => $request->get('modal_id'),
            'question_types' => QuestionType::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @access public
     * @param  StoreEventQuestionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreateEventQuestion(StoreEventQuestionRequest $request, $event_id)
    {

        // Get the event or display a 'not found' warning.
        $event = Event::findOrFail($event_id);

        // Create question.
        $question = Question::createNew(false, false, true);
        $question->title = $request->get('title');
        $question->instructions = $request->get('instructions');
        $question->is_required = ($request->get('is_required') == 'yes') ;
        $question->question_type_id = $request->get('question_type_id');
        $question->save();

        // Get options.
        $options = $request->get('option');

        // Add options.
        if ($options && is_array($options)) {
            foreach ($options as $option_name) {
                if (trim($option_name) !== '') {
                    $question->options()->create([
                        'name' => $option_name,
                    ]);
                }
            }
        }

        // Get tickets.
        $ticket_ids = $request->get('tickets');

        $question->tickets()->attach($ticket_ids);

        $event->questions()->attach($question->id);

        session()->flash('message', 'Successfully Created Question');

        return response()->json([
            'status' => 'success',
            'message' => 'Refreshing..',
            'redirectUrl' => '',
        ]);
    }


    public function showEditEventQuestion(Request $request, $event_id, $question_id)
    {
        $question = Question::scope()->findOrFail($question_id);
        $event = Event::scope()->findOrFail($event_id);

        $data = [
            'question' => $question,
            'event' => $event,
            'question_types' => QuestionType::all(),
            'modal_id' => $request->get('modal_id'),
        ];

        return view('ManageEvent.Modals.EditQuestion', $data);
    }


    public function postEditEventQuestion(Request $request, $event_id, $question_id)
    {
        // Get the event or display a 'not found' warning.
        $event = Event::scope()->findOrFail($event_id);

        // Create question.
        $question = Question::scope()->findOrFail($question_id);
        $question->title = $request->get('title');
        $question->instructions = $request->get('instructions');
        $question->is_required = $request->get('is_required');
        $question->question_type_id = $request->get('question_type_id');
        $question->save();

        // Get options.
        $options = $request->get('option');

        $question->options()->delete();

        // Add options.
        if ($options && is_array($options)) {
            foreach ($options as $option_name) {
                if (trim($option_name) !== '') {
                    $question->options()->create([
                        'name' => $option_name,
                    ]);
                }
            }
        }

        // Get tickets.
        $ticket_ids = $request->get('tickets');

        $question->tickets()->sync($ticket_ids);

        session()->flash('message', 'Successfully Edited Question');

        return response()->json([
            'status' => 'success',
            'message' => 'Refreshing..',
            'redirectUrl' => '',
        ]);

    }

    public function postDeleteEventQuestion(Request $request, $event_id)
    {
        $question_id = $request->get('question_id');

        $question = Question::scope()->find($question_id);

        if ($question->delete()) {

            session()->flash('message', 'Question Successfully Deleted');

            return response()->json([
                'status'  => 'success',
                'message' => 'Refreshing..',
                'redirectUrl' => route('showEventCustomize', ['event_id' => $event_id]) . '#questions',
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'id'      => $question->id,
            'message' => 'This question can\'t be deleted.',
        ]);
    }

}
