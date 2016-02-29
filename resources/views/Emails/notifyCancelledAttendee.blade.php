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
<br>
<p style="color:#999;">
    You have received this message as you are listed as an attendee on an event which was created with <a href="http://attendize.com/?utm_source=email_footer">Attendize Ticketing</a>. 
</p>
<br>
<p style="color:#999;">
    If you have any questions, simply contact us at <a href='mailto:{{INCOMING_EMAIL}}'>{{INCOMING_EMAIL}}</a> and we'll be happy to help.
</p>

@stop
