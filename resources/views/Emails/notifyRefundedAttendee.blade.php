@extends('Emails.Layouts.Master')

@section('message_content')

    <p>Hi there,</p>
    <p>
        You have received a refund on behalf of your cancelled ticket for <b>{{{$attendee->event->title}}}</b>.
        <b>{{{ $refund_amount }}} has been refunded to the original payee, you should see the payment in a few days.</b>
    </p>

    <p>
        You can contact <b>{{{ $attendee->event->organiser->name }}}</b> directly at <a href='mailto:{{{$attendee->event->organiser->email}}}'>{{{$attendee->event->organiser->email}}}</a> or by replying to this email should you require any more information.
    </p>
@stop

@section('footer')

@stop