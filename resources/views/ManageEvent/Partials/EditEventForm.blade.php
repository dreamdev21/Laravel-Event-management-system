@include('ManageOrganiser.Partials.EventCreateAndEditJS')

{!! Form::model($event, array('url' => route('postEditEvent', ['event_id' => $event->id]), 'class' => 'ajax gf')) !!}

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('is_live', trans('manageevent.event-visibility'), array('class'=>'control-label required')) !!}
            {!!  Form::select('is_live', [
            '1' => trans('manageevent.make-event-visible'),
            '0' => trans('manageevent.hide-event-visible')],null,
                                        array(
                                        'class'=>'form-control'
                                        ))  !!}
        </div>
        <div class="form-group">
            {!! Form::label('title', trans('manageorganiser.event-title'), array('class'=>'control-label required')) !!}
            {!!  Form::text('title', Input::old('title'),
                                        array(
                                        'class'=>'form-control',
                                        'placeholder'=>'E.g: '.Auth::user()->first_name.'\'s Interational Conference'
                                        ))  !!}
        </div>

        <div class="form-group">
           {!! Form::label('description', trans('manageorganiser.event-description'), array('class'=>'control-label')) !!}
            {!!  Form::textarea('description', Input::old('description'),
                                        array(
                                        'class'=>'form-control editable',
                                        'rows' => 5
                                        ))  !!}
        </div>

        <div class="form-group address-automatic" style="display:{{$event->location_is_manual ? 'none' : 'block'}};">
            {!! Form::label('name', trans('manageorganiser.venue-name'), array('class'=>'control-label required ')) !!}
            {!!  Form::text('venue_name_full', Input::old('venue_name_full'),
                                        array(
                                        'class'=>'form-control geocomplete location_field',
                                        'placeholder'=>'E.g: The Crab Shack'
                                        ))  !!}

            <!--These are populated with the Google places info-->
            <div>
               {!! Form::hidden('formatted_address', $event->location_address, ['class' => 'location_field']) !!}
               {!! Form::hidden('street_number', $event->location_street_number, ['class' => 'location_field']) !!}
               {!! Form::hidden('country', $event->location_country, ['class' => 'location_field']) !!}
               {!! Form::hidden('country_short', $event->location_country_short, ['class' => 'location_field']) !!}
               {!! Form::hidden('place_id', $event->location_google_place_id, ['class' => 'location_field']) !!}
               {!! Form::hidden('name', $event->venue_name, ['class' => 'location_field']) !!}
               {!! Form::hidden('location', '', ['class' => 'location_field']) !!}
               {!! Form::hidden('postal_code', $event->location_post_code, ['class' => 'location_field']) !!}
               {!! Form::hidden('route', $event->location_address_line_1, ['class' => 'location_field']) !!}
               {!! Form::hidden('lat', $event->location_lat, ['class' => 'location_field']) !!}
               {!! Form::hidden('lng', $event->location_long, ['class' => 'location_field']) !!}
               {!! Form::hidden('administrative_area_level_1', $event->location_state, ['class' => 'location_field']) !!}
               {!! Form::hidden('sublocality', '', ['class' => 'location_field']) !!}
               {!! Form::hidden('locality', $event->location_address_line_1, ['class' => 'location_field']) !!}
            </div>
            <!-- /These are populated with the Google places info-->

        </div>

        <div class="address-manual" style="display:{{$event->location_is_manual ? 'block' : 'none'}};">
            <div class="form-group">
                {!! Form::label('location_venue_name', trans('manageorganiser.venue-name'), array('class'=>'control-label required ')) !!}
                {!!  Form::text('location_venue_name', $event->venue_name, [
                                        'class'=>'form-control location_field',
                                        'placeholder'=>'E.g: The Crab Shack'
                            ])  !!}
            </div>
            <div class="form-group">
                {!! Form::label('location_address_line_1', trans('manageorganiser.address-line-1'), array('class'=>'control-label')) !!}
                {!!  Form::text('location_address_line_1', $event->location_address_line_1, [
                                        'class'=>'form-control location_field',
                                        'placeholder'=>'E.g: 45 Grafton St.'
                            ])  !!}
            </div>
            <div class="form-group">
                {!! Form::label('location_address_line_2', trans('manageorganiser.address-line-2'), array('class'=>'control-label')) !!}
                {!!  Form::text('location_address_line_2', $event->location_address_line_2, [
                                        'class'=>'form-control location_field',
                                        'placeholder'=>'E.g: Dublin.'
                            ])  !!}
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('location_state', trans('manageorganiser.city'), array('class'=>'control-label')) !!}
                        {!!  Form::text('location_state', $event->location_state, [
                                        'class'=>'form-control location_field',
                                        'placeholder'=>'E.g: Dublin.'
                            ])  !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('location_post_code', trans('manageorganiser.post-code'), array('class'=>'control-label')) !!}
                        {!!  Form::text('location_post_code', $event->location_post_code, [
                                        'class'=>'form-control location_field',
                                        'placeholder'=>'E.g: 94568.'
                            ])  !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="clearfix" style="margin-top:-10px; padding: 5px; padding-top: 0px;">
            <span class="pull-right">
                or <a data-clear-field=".location_field" data-toggle-class=".address-automatic, .address-manual" data-show-less-text="{{$event->location_is_manual ? trans('manageevent.enter-address') : trans('manageevent.select-from-venues')}}" href="javascript:void(0);" class="show-more-options clear_location">{{$event->location_is_manual ? 'Select From Existing Venues' : 'Enter Address Manually'}}</a>
            </span>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('start_date', trans('manageorganiser.event-start-date'), array('class'=>'required control-label')) !!}
                    {!!  Form::text('start_date', $event->getFormattedDate('start_date'),
                                                        [
                                                    'class'=>'form-control start hasDatepicker ',
                                                    'data-field'=>'datetime',
                                                    'data-startend'=>'start',
                                                    'data-startendelem'=>'.end',
                                                    'readonly'=>''

                                                ])  !!}
                </div>
            </div>

            <div class="col-sm-6 ">
                <div class="form-group">
                    {!!  Form::label('end_date', trans('manageorganiser.event-end-date'),
                                        [
                                    'class'=>'required control-label '
                                ])  !!}
                    {!!  Form::text('end_date', $event->getFormattedDate('end_date'),
                                                [
                                            'class'=>'form-control end hasDatepicker ',
                                            'data-field'=>'datetime',
                                            'data-startend'=>'end',
                                            'data-startendelem'=>'.start',
                                            'readonly'=>''
                                        ])  !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                   {!! Form::label('event_image', trans('manageevent.event-flyer'), array('class'=>'control-label ')) !!}
                   {!! Form::styledFile('event_image', 1) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="float-l">
                    @if($event->images->count())
                    {!! Form::label('', trans('manageevent.current-event-flyer'), array('class'=>'control-label ')) !!}
                    <div class="form-group">
                        <div class="well well-sm well-small">
                           {!! Form::label('remove_current_image', trans('manageevent.delete-question'), array('class'=>'control-label ')) !!}
                           {!! Form::checkbox('remove_current_image') !!}

                        </div>
                    </div>
                    <div class="thumbnail">
                       {!!HTML::image('/'.$event->images->first()['image_path'])!!}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel-footer mt15 text-right">
           {!! Form::hidden('organiser_id', $event->organiser_id) !!}
           {!! Form::submit(trans('manageevent.save-changes'), ['class'=>"btn btn-success"]) !!}
        </div>
    </div>
    {!! Form::close() !!}
</div>

