<?php

namespace App\Http\Controllers;

class EventTicketQuestionsController extends MyBaseController
{
    public function showQuestions($event_id)
    {
        $data = [
            'event'          => Event::scope()->findOrFail($event_id),
            'modal_id'       => Input::get('modal_id'),
            'question_types' => QuestionType::all(),
        ];

        return View::make('ManageEvent.Modals.ViewQuestions', $data);
    }

    public function postCreateQuestion($event_id)
    {
        $event = Event::findOrFail($event_id);

        $question = Question::createNew(false, false, true);
        $question->title = Input::get('title');
        $question->instructions = Input::get('instructions');
        $question->options = Input::get('options');
        $question->is_required = Input::get('title');
        $question->question_type_id = Input::get('question_type_id');
        $question->save();

        $ticket_ids = Input::get('tickets');

        foreach ($ticket_ids as $ticket_id) {
            Ticket::scope()->find($ticket_id)->questions()->attach($question->id);
        }

        $event->questions()->attach($question->id);

        return Response::json([
           'status'   => 'success',
            'message' => 'Successfully Created Question',
        ]);
    }
}
