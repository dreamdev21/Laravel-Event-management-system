@extends('Emails.Layouts.Master')

@section('message_content')

<p>Hello</p>
<p>
    You have been added to an Attendize Ticketing account by {{$inviter->first_name.' '.$inviter->last_name}}.
</p>

<p>
    You can log in using the following details.<br><br>
    
    Username: <b>{{$user->email}}</b> <br>
    Password: <b>{{$temp_password}}</b>
</p>

<p>
    You can change your temporary password once you have logged.
</p>

<div style="padding: 5px; border: 1px solid #ccc;" >
   {{route('login')}}
</div>
<br><br>
<p>
    If you have any questions, feedback or suggestions feel free to reply to this email.
</p>
<p>
    Thank you,<br>
    The Attendize team.
</p>

@stop

@section('footer')


@stop
