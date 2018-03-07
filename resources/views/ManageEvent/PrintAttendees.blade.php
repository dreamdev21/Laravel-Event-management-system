<html>
    <head>
        <title>
            {{ trans('manageevent.attendees') }}
        </title>

        <!--Style-->
       {!!HTML::style('assets/stylesheet/application.css')!!}
        <!--/Style-->

        <style type="text/css">
            .table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td {
                padding: 3px;
            }
            table {
                font-size: 13px;
            }
        </style>
    </head>
    <body style="background-color: #FFFFFF;" onload="window.print();">
        <div class="well" style="border:none; margin: 0;">
            <b>{{$attendees->count()}}</b> {{ trans('manageevent.print-attendees-event') }} <b>{{{$event->title}}}</b> ({{$event->start_date->toDayDateTimeString()}})<br>
        </div>

        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>{{ trans('common.name') }}</th>
                    <th>{{ trans('common.email') }}</th>
                    <th>{{ trans('common.ticket') }}</th>
                    <th>{{ trans('manageevent.order-ref') }}</th>
                    <th>{{ trans('common.purchase-date') }}</th>
                    <th>{{ trans('common.arrived') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendees as $attendee)
                <tr>
                    <td>{{{$attendee->full_name}}}</td>
                    <td>{{{$attendee->email}}}</td>
                    <td>{{{$attendee->ticket->title}}}</td>
                    <td>{{{$attendee->order->order_reference}}}</td>
                    <td>{{$attendee->created_at->format('d/m/Y H:i')}}</td>
                    <td><input type="checkbox" style="border: 1px solid #000; height: 15px; width: 15px;" /></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>