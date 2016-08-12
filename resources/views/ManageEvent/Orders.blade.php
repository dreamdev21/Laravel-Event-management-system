@extends('Shared.Layouts.Master')

@section('title')
@parent

Event Orders
@stop

@section('top_nav')
@include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
@include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
<i class='ico-cart mr5'></i>
Event Orders
<span class="page_title_sub_title hide">
    Showing 30 orders out of <b>{{\App\Models\Order::scope()->count()}}</b> Total
</span>
@stop

@section('head')

@stop

@section('page_header')
<div class="col-md-9 col-sm-6">
    <!-- Toolbar -->
    <div class="btn-toolbar" role="toolbar">
        <div class="btn-group btn-group btn-group-responsive">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                <i class="ico-users"></i> Export <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{{route('showExportOrders', ['event_id'=>$event->id,'export_as'=>'xlsx'])}}">Excel (XLSX)</a></li>
                <li><a href="{{route('showExportOrders', ['event_id'=>$event->id,'export_as'=>'xls'])}}">Excel (XLS)</a></li>
                <li><a href="{{route('showExportOrders', ['event_id'=>$event->id,'export_as'=>'csv'])}}">CSV</a></li>
                <li><a href="{{route('showExportOrders', ['event_id'=>$event->id,'export_as'=>'html'])}}">HTML</a></li>
            </ul>
        </div>
    </div>
    <!--/ Toolbar -->
</div>
<div class="col-md-3 col-sm-6">
   {!! Form::open(array('url' => route('showEventOrders', ['event_id'=>$event->id,'sort_by'=>$sort_by]), 'method' => 'get')) !!}
    <div class="input-group">
        <input name='q' value="{{$q or ''}}" placeholder="Search Orders.." type="text" class="form-control">
        <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="ico-search"></i></button>
        </span>
    </div>
   {!! Form::close() !!}
</div>
@stop


@section('content')
<!--Start Attendees table-->
<div class="row">

    @if($orders->count())

    <div class="col-md-12">

        <!-- START panel -->
        <div class="panel">
            <div class="table-responsive ">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                               {!! Html::sortable_link('Order Ref.', $sort_by, 'order_reference', $sort_order, ['q' => $q , 'page' => $orders->currentPage()]) !!}
                            </th>
                            <th>
                               {!! Html::sortable_link('Order Date', $sort_by, 'created_at', $sort_order, ['q' => $q , 'page' => $orders->currentPage()]) !!}
                            </th>
                            <th>
                               {!! Html::sortable_link('Name', $sort_by, 'first_name', $sort_order, ['q' => $q , 'page' => $orders->currentPage()]) !!}
                            </th>
                            <th>
                               {!! Html::sortable_link('Email', $sort_by, 'email', $sort_order, ['q' => $q , 'page' => $orders->currentPage()]) !!}
                            </th>
                            <th>
                               {!! Html::sortable_link('Amount', $sort_by, 'amount', $sort_order, ['q' => $q , 'page' => $orders->currentPage()]) !!}
                            </th>
                            <th>
                               {!! Html::sortable_link('Status', $sort_by, 'order_status_id', $sort_order, ['q' => $q , 'page' => $orders->currentPage()]) !!}
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <a href='javascript:void(0);' data-modal-id='view-order-{{ $order->id }}' data-href="{{route('showManageOrder', ['order_id'=>$order->id])}}" title="View Order #{{$order->order_reference}}" class="loadModal">
                                    {{$order->order_reference}}
                                </a>
                            </td>
                            <td>
                                {{ $order->created_at->toDayDateTimeString() }}
                            </td>
                            <td>
                                {{$order->first_name.' '.$order->last_name}}
                            </td>
                            <td>
                                <a href="javascript:void(0);" class="loadModal"
                                    data-modal-id="MessageOrder"
                                    data-href="{{route('showMessageOrder', ['order_id'=>$order->id])}}"
                                > {{$order->email}}</a>
                            </td>
                            <td>
                                <a href="#" class="hint--top" data-hint="{{money($order->amount, $event->currency)}} + {{money($order->organiser_booking_fee, $event->currency)}} Organiser Booking Fee">
                                    {{money($order->amount + $order->organiser_booking_fee, $event->currency)}}
                                    @if($order->is_refunded || $order->is_partially_refunded)

                                    @endif
                                </a>
                            </td>
                            <td>
                                <span class="label label-{{(!$order->is_payment_received || $order->is_refunded || $order->is_partially_refunded) ? 'warning' : 'success'}}">
                                    {{$order->orderStatus->name}}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0);" data-modal-id="cancel-order-{{ $order->id }}" data-href="{{route('showCancelOrder', ['order_id'=>$order->id])}}" title="Cancel Order" class="btn btn-xs btn-danger loadModal">
                                                Refund / Cancel
                                            </a>
                                <a data-modal-id="view-order-{{ $order->id }}" data-href="{{route('showManageOrder', ['order_id'=>$order->id])}}" title="View Order" class="btn btn-xs btn-primary loadModal">Details</a>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        {!!$orders->appends(['sort_by' => $sort_by, 'sort_order' => $sort_order, 'q' => $q])->render()!!}
    </div>

    @else

    @if($q)
    @include('Shared.Partials.NoSearchResults')
    @else
    @include('ManageEvent.Partials.OrdersBlankSlate')
    @endif

    @endif
</div>    <!--/End attendees table-->
@stop
