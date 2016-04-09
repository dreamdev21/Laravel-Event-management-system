@extends('Emails.Layouts.Master')

@section('message_content')

<p>Hi there,</p>
<p>
    Your ticket for the event <b>{{{$attendee->event->title}}}</b> has been cancelled.
</p>

<p>
    You can contact <b>{{{$attendee->event->organiser->name}}}</b> directly at <a href='mailto:{{{$attendee->event->organiser->email}}}'>{{{$attendee->event->organiser->email}}}</a> or by replying to this email should you require any more information.
</p>
@stop

@section('footer')

@stop
