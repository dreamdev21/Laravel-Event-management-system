<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventQuestionRequest;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Question;
use App\Models\QuestionType;
use Illuminate\Http\Request;

class EventQuestionsController extends MyBaseController
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Get the event ID.
        $event_id = $request->get('event_id');

        // Get the event or display a 'not found' warning.
        $event = Event::findOrFail($event_id);

        // Initialize an empty array for the question options.
        $question_options = [
            (object)[
                'name' => '',
            ],
        ];

        return view('ManageEvent.Modals.CreateQuestion', [
            'form_url' => route('event.question.store', [
                'event_id' => $event_id,
            ]),
            'event' => $event,
            'question' => [],
            'modal_id' => $request->get('modal_id'),
            'question_types' => QuestionType::all(),
            'question_options' => $question_options,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @access public
     * @param  StoreEventQuestionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEventQuestionRequest $request)
    {
        // Get the event ID.
        $event_id = $request->get('event_id');

        // Get the event or display a 'not found' warning.
        $event = Event::findOrFail($event_id);

        // Create question.
        $question = Question::createNew(false, false, true);
        $question->title = $request->get('title');
        $question->instructions = $request->get('instructions');
        $question->is_required = $request->get('title');
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

        // Add ticket <-> question entry.
        if ($ticket_ids && is_array($ticket_ids)) {
            foreach ($ticket_ids as $ticket_id) {
                Ticket::find($ticket_id)->questions()->attach($question->id);
            }
        }

        $event->questions()->attach($question->id);

        return response()->json([
            'status'   => 'success',
            'message' => 'Successfully Created Question. Refreshing page...',
            'runThis' => 'reloadPageDelayed();',
        ]);
    }


    public function showEditEventQuestion(Request $request, $event_id, $question_id)
    {
        $question = Question::findOrFail($question_id);
        $event = Event::findOrFail($event_id);

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

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}