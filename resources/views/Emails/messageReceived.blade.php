@extends('Emails.Layouts.Master')

@section('message_content')

<p>Hi there,</p>
<p>You have received a message from <b>{{ (isset($sender_name) ? $sender_name : $event->organiser->name) }}</b> in relation to the event <b>{{ $event->title }}</b>.</p>
<p style="padding: 10px; margin:10px; border: 1px solid #f3f3f3;">
    {{nl2br($message_content)}}
</p>

<p>
    You can contact <b>{{ (isset($sender_name) ? $sender_name : $event->organiser->name) }}</b> directly at <a href='mailto:{{ (isset($sender_email) ? $sender_email : $event->organiser->email) }}'>{{ (isset($sender_email) ? $sender_email : $event->organiser->email) }}</a>, or by replying to this email.
</p>
@stop

@section('footer')


@stop
