{{--

@todo Rewrite the JS for choosing event bg images and colours.

--}}

@extends('Shared.Layouts.Master')

@section('title')
    @parent
    Customize Event
@stop

@section('top_nav')
    @include('ManageEvent.Partials.TopNav')
@stop

@section('menu')
    @include('ManageEvent.Partials.Sidebar')
@stop

@section('page_title')
    <i class="ico-cog mr5"></i>
    Customize Event
@stop

@section('page_header')
    <style>
        .page-header {
            display: none;
        }
    </style>
@stop

@section('head')
    {!! HTML::script('https://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places') !!}
    {!! HTML::script('vendor/geocomplete/jquery.geocomplete.min.js') !!}
    <script>
        $(function () {

            $("input[name='organiser_fee_percentage']").TouchSpin({
                min: 0,
                max: 100,
                step: 0.1,
                decimals: 2,
                verticalbuttons: true,
                postfix: '%',
                buttondown_class: "btn btn-link",
                buttonup_class: "btn btn-link",
                postfix_extraclass: "btn btn-link"
            });
            $("input[name='organiser_fee_fixed']").TouchSpin({
                min: 0,
                max: 100,
                step: 0.1,
                decimals: 2,
                verticalbuttons: true,
                postfix: '{{$event->currency->symbol_left}}',
                buttondown_class: "btn btn-link",
                buttonup_class: "btn btn-link",
                postfix_extraclass: "btn btn-link"
            });

            /* Affiliate generator */
            $('#affiliateGenerator').on('keyup', function () {
                var text = $(this).val().replace(/\W/g, ''),
                        referralUrl = '{{$event->event_url}}?ref=' + text;

                $('#referralUrl').toggle(text !== '');
                $('#referralUrl input').val(referralUrl);
            });

            /* Background selector */
            $('.bgImage').on('click', function (e) {
                $('.bgImage').removeClass('selected');
                $(this).addClass('selected');
                $('input[name=bg_image_path_custom]').val($(this).data('src'));

                var replaced = replaceUrlParam('{{route('showEventPagePreview', ['event_id'=>$event->id])}}', 'bg_img_preview', $('input[name=bg_image_path_custom]').val());
                document.getElementById('previewIframe').src = replaced;
                e.preventDefault();
            });

            /* Background color */
            $('input[name=bg_color]').on('change', function(e) {
                var replaced = replaceUrlParam('{{route('showEventPagePreview', ['event_id'=>$event->id])}}', 'bg_color_preview', $('input[name=bg_color]').val().substring(1));
                document.getElementById('previewIframe').src = replaced;
                e.preventDefault();
            });

            $('#bgOptions .panel').on('shown.bs.collapse', function (e) {
                var type = $(e.currentTarget).data('type');
                console.log(type);
                $('input[name=bg_type]').val(type);
            });

            $('input[name=bg_image_path], input[name=bg_color]').on('change', function () {
                //showMessage('Uploading...');
                //$('.customizeForm').submit();
            });
        });
        function replaceUrlParam(url, paramName, paramValue){
            var pattern = new RegExp('\\b('+paramName+'=).*?(&|$)')
            if(url.search(pattern)>=0){
                return url.replace(pattern,'$1' + paramValue + '$2');
            }
            return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue
        }
    </script>

    <style type="text/css">
        .bootstrap-touchspin-postfix {
            background-color: #ffffff;
            color: #333;
            border-left: none;
        }

        .bgImage {
            cursor: pointer;
        }

        .bgImage.selected {
            outline: 4px solid #0099ff;
        }
    </style>
    <script>
        $(function () {

            var hash = document.location.hash;
            var prefix = "tab_";
            if (hash) {
                $('.nav-tabs a[href=' + hash + ']').tab('show');
            }

            $(window).on('hashchange', function () {
                var newHash = location.hash;
                if (typeof newHash === undefined) {
                    $('.nav-tabs a[href=' + '#general' + ']').tab('show');
                } else {
                    $('.nav-tabs a[href=' + newHash + ']').tab('show');
                }

            });

            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
            });

        });


    </script>

@stop


@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- tab -->
            <ul class="nav nav-tabs">
                <li data-route="{{route('showEventCustomizeTab', ['event_id' => $event->id, 'tab' => 'general'])}}"
                    class="{{($tab == 'general' || !$tab) ? 'active' : ''}}"><a href="#general" data-toggle="tab">General</a>
                </li>
                <li data-route="{{route('showEventCustomizeTab', ['event_id' => $event->id, 'tab' => 'design'])}}"
                    class="{{$tab == 'design' ? 'active' : ''}}"><a href="#design" data-toggle="tab">Event Page
                        Design</a></li>
                <li data-route="{{route('showEventCustomizeTab', ['event_id' => $event->id, 'tab' => 'order_page'])}}"
                    class="{{$tab == 'order_page' ? 'active' : ''}}"><a href="#order_page" data-toggle="tab">Order
                        Form</a></li>
                <li data-route="{{route('showEventCustomizeTab', ['event_id' => $event->id, 'tab' => 'social'])}}"
                    class="{{$tab == 'social' ? 'active' : ''}}"><a href="#social" data-toggle="tab">Social</a></li>
                <li data-route="{{route('showEventCustomizeTab', ['event_id' => $event->id, 'tab' => 'affiliates'])}}"
                    class="{{$tab == 'affiliates' ? 'active' : ''}}"><a href="#affiliates"
                                                                        data-toggle="tab">Affiliates</a></li>
                <li data-route="{{route('showEventCustomizeTab', ['event_id' => $event->id, 'tab' => 'fees'])}}"
                    class="{{$tab == 'fees' ? 'active' : ''}}"><a href="#fees" data-toggle="tab">Service Fees</a></li>
                <li data-route="{{route('showEventCustomizeTab', ['event_id' => $event->id, 'tab' => 'ticket_design'])}}"
                    class="{{$tab == 'ticket_design' ? 'active' : ''}}"><a href="#ticket_design" data-toggle="tab">Ticket Design</a></li>
                <li data-route="{{route('showEventCustomizeTab', ['event_id' => $event->id, 'tab' => 'embed'])}}"
                    class="{{$tab == 'embed' ? 'active' : ''}}"><a href="#embed" data-toggle="tab">Website Embed
                        Code</a></li>
            </ul>
            <!--/ tab -->
            <!-- tab content -->
            <div class="tab-content panel">
                <div class="tab-pane {{($tab == 'general' || !$tab) ? 'active' : ''}}" id="general">
                    @include('ManageEvent.Partials.EditEventForm', ['event'=>$event, 'organisers'=>\Auth::user()->account->organisers])
                </div>

                <div class="tab-pane {{$tab == 'affiliates' ? 'active' : ''}}" id="affiliates">

                    <h4>Affiliate Tracking</h4>

                    <div class="well">
                        Keeping track of who is generating sales for your event is extremely easy.
                        Simply create a referral link using the box below and share the link with your affiliates /
                        event promoters.

                        <br><br>

                        <input type="text" id="affiliateGenerator" name="affiliateGenerator" class="form-control"/>

                        <div style="display:none; margin-top:10px; " id="referralUrl">
                            <input onclick="this.select();" type="text" name="affiliateLink" class="form-control"/>
                        </div>
                    </div>

                    @if($event->affiliates->count())
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>
                                        Affiliate Name
                                    </th>
                                    <th>
                                        Visits Generated
                                    </th>
                                    <th>
                                        Ticket Sales Generated
                                    </th>
                                    <th>
                                        Sales Volume Generated
                                    </th>
                                    <th>
                                        Last Referral
                                    </th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($event->affiliates as $affiliate)
                                    <tr>
                                        <td>
                                            {{{$affiliate->name}}}
                                        </td>
                                        <td>
                                            {{$affiliate->visits}}
                                        </td>
                                        <td>
                                            {{$affiliate->tickets_sold}}
                                        </td>
                                        <td>
                                            {{money($affiliate->sales_volume, $event->currency->code)}}
                                        </td>
                                        <td>
                                            {{{ $affiliate->updated_at->format('M dS H:i A') }}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No affiliate referrals yet.
                        </div>
                    @endif


                </div>
                <div class="tab-pane {{$tab == 'social' ? 'active' : ''}}" id="social">
                    <div class="well hide">
                        <h5>The following short codes are available for use:</h5>
                        Display the event's public URL: <code>[event_url]</code><br>
                        Display the organiser's name: <code>[organiser_name]</code><br>
                        Display the event title: <code>[event_title]</code><br>
                        Display the event description: <code>[event_description]</code><br>
                        Display the event start date & time: <code>[event_start_date]</code><br>
                        Display the event end date & time: <code>[event_end_date]</code>
                    </div>

                    {!! Form::model($event, array('url' => route('postEditEventSocial', ['event_id' => $event->id]), 'class' => 'ajax ')) !!}

                    <h4>Social Settings</h4>

                    <div class="form-group hide">

                        {!! Form::label('social_share_text', 'Social Share Text', array('class'=>'control-label ')) !!}

                        {{!!  Form::textarea('social_share_text', $event->social_share_text, [
                            'class' => 'form-control',
                            'rows' => 4
                        ])  !!}}
                        <div class="help-block">
                            This is the text which will be share by default when a user shares your event on social
                            networks
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="control-label">Share buttons to show.</label>
                        <br>

                        <div class="checkbox-inline">
                            <label>
                                {!! Form::checkbox('social_show_facebook', 1, $event->social_show_facebook) !!}
                                Facebook
                            </label>
                        </div>
                        <div class="checkbox-inline">
                            <label>
                                {!! Form::checkbox('social_show_twitter', 1, $event->social_show_twitter) !!}
                                Twitter
                            </label>
                        </div>
                        <div class="checkbox-inline">
                            <label>
                                {!! Form::checkbox('social_show_googleplus', 1, $event->social_show_googleplus) !!}
                                Google+
                            </label>
                        </div>
                        <div class="checkbox-inline">
                            <label>
                                {!! Form::checkbox('social_show_email', 1, $event->social_show_email) !!}
                                Email
                            </label>
                        </div>
                        <div class="checkbox-inline">
                            <label>
                                {!! Form::checkbox('social_show_linkedin', 1, $event->social_show_linkedin) !!}
                                LinkedIn
                            </label>
                        </div>
                    </div>

                    <div class="panel-footer mt15 text-right">
                        {!! Form::submit('Save Changes', ['class'=>"btn btn-success"]) !!}
                    </div>

                    {!! Form::close() !!}

                </div>
                <div class="tab-pane {{$tab == 'design' ? 'active' : ''}}" id="design">

                    <div class="row">
                        <div class="col-sm-6">

                            {!! Form::open(array('url' => route('postEditEventDesign', ['event_id' => $event->id]), 'files'=> true, 'class' => 'ajax customizeForm')) !!}

                            {!! Form::hidden('bg_type', $event->bg_type) !!}

                            <h4>Background Options</h4>

                            <div class="panel-group" id="bgOptions">

                                <div class="panel panel-default" data-type="color">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#bgOptions" href="#bgColor"
                                               class="{{($event->bg_type == 'color') ? '' : 'collapsed'}}">
                                                <span class="arrow mr5"></span> Use a colour for the background
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="bgColor"
                                         class="panel-collapse {{($event->bg_type == 'color') ? 'in' : 'collapse'}}">
                                        <div class="panel-body">
                                            <input value="{{{$event->bg_color}}}" type="color" name="bg_color"/>
                                        </div>
                                    </div>
                                </div>


                                <div class="panel panel-default" data-type="image">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#bgOptions" href="#bgImage"
                                               class="{{($event->bg_type == 'image') ? '' : 'collapsed'}}">
                                                <span class="arrow mr5"></span> Select from available images
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="bgImage"
                                         class="panel-collapse {{($event->bg_type == 'image') ? 'in' : 'collapse'}}">
                                        <div class="panel-body">
                                            @foreach($available_bg_images_thumbs as $bg_image)

                                                <img data-3="{{str_replace('/thumbs', '', $event->bg_image_path)}}"
                                                     class="img-thumbnail ma5 bgImage {{ ($bg_image === str_replace('/thumbs', '', $event->bg_image_path) ? 'selected' : '') }}"
                                                     style="width: 120px;" src="{{asset($bg_image)}}"
                                                     data-src="{{str_replace('/thumbs', '', substr($bg_image,1))}}"/>

                                            @endforeach

                                            {!! Form::hidden('bg_image_path_custom', ($event->bg_type == 'image') ? $event->bg_image_path : '') !!}
                                        </div>
                                    </div>
                                </div>

                                {{--
                                ## Ability to have a custom background image is disbaled for now.

                                                            <div class="panel panel-default" data-type="custom_image">
                                                                <div class="panel-heading">
                                                                    <h4 class="panel-title">
                                                                        <a data-toggle="collapse" data-parent="#bgOptions" href="#bgCustomImage" class="{{($event->bg_type == 'custom_image') ? '' : 'collapsed'}}">
                                                                            <span class="arrow mr5"></span> Use my own image
                                                                        </a>
                                                                    </h4>
                                                                </div>
                                                                <div id="bgCustomImage" class="panel-collapse {{($event->bg_type == 'custom_image') ? 'in' : 'collapse '}}" >
                                                                    <div class="panel-body">
                                                                        <div class="form-group">
                                                                           {!! Form::styledFile('bg_image_path') !!}
                                                                        </div>

                                                                        @if($event->bg_type == 'custom_image')
                                                                        <!--                                        <h5>
                                                                                                                    Current Image
                                                                                                                </h5>
                                                                                                                <img style="max-width: 220px;" class="img-thumbnail" src="/{{$event->bg_image_path}}" />-->
                                                                        @endif

                                                                    </div>
                                                                </div>
                                                            </div>
                                --}}

                            </div>
                            <div class="panel-footer mt15 text-right">
                                <span class="uploadProgress" style="display:none;"></span>
                                {!! Form::submit('Save Changes', ['class'=>"btn btn-success"]) !!}
                            </div>


                            <div class="panel-footer ar hide">
                                {!! Form::button('Cancel', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                                {!! Form::submit('Save Changes', ['class'=>"btn btn-success"]) !!}
                            </div>

                            {!! Form::close() !!}

                        </div>
                        <div class="col-sm-6">
                            <h4>Event Page Preview</h4>

                            <div style="height: 600px; border: 1px solid #ccc;">
                                <iframe id="previewIframe"
                                        src="{{route('showEventPagePreview', ['event_id'=>$event->id])}}"
                                        frameborder="0" style="overflow:hidden;height:100%;width:100%" height="100%"
                                        width="100%">
                                </iframe>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="tab-pane {{$tab == 'fees' ? 'active' : ''}}" id="fees">
                    {!! Form::model($event, array('url' => route('postEditEventFees', ['event_id' => $event->id]), 'class' => 'ajax')) !!}
                    <h4>Organiser Fees</h4>

                    <div class="well">
                        These are optional fees you can include in the cost of each ticket. This charge will appear on buyer's invoices as '<b>BOOKING FEES</b>'.
                    </div>

                    <div class="form-group">
                        {!! Form::label('organiser_fee_percentage', 'Service Fee Percentage', array('class'=>'control-label required')) !!}
                        {!!  Form::text('organiser_fee_percentage', $event->organiser_fee_percentage, [
                            'class' => 'form-control',
                            'placeholder' => '0'
                        ])  !!}
                        <div class="help-block">
                            e.g: enter <b>3.5</b> for <b>3.5%</b>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('organiser_fee_fixed', 'Service Fee Fixed Price', array('class'=>'control-label required')) !!}
                        {!!  Form::text('organiser_fee_fixed', null, [
                            'class' => 'form-control',
                            'placeholder' => '0.00'
                        ])  !!}
                        <div class="help-block">
                            e.g: enter <b>1.25</b> for <b>{{$event->currency_symbol}}1.25</b>
                        </div>
                    </div>
                    <div class="panel-footer mt15 text-right">
                        {!! Form::submit('Save Changes', ['class'=>"btn btn-success"]) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
                <div class="tab-pane" id="social">
                    <h4>Social Settings</h4>

                    <div class="form-group">
                        <div class="checkbox custom-checkbox">
                            {!! Form::label('event_page_show_map', 'Show map on event page?', array('id' => 'customcheckbox', 'class'=>'control-label')) !!}
                            {!! Form::checkbox('event_page_show_map', 1, false) !!}
                        </div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('event_page_show_social_share', 'Show social share buttons?', array('class'=>'control-label')) !!}
                        {!! Form::checkbox('event_page_show_social_share', 1, false) !!}
                    </div>

                </div>

                <div class="tab-pane {{$tab == 'order_page' ? 'active' : ''}}" id="order_page">
                    {!! Form::model($event, array('url' => route('postEditEventOrderPage', ['event_id' => $event->id]), 'class' => 'ajax ')) !!}
                    <h4>Order Page Settings</h4>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('ask_for_all_attendees_info', 'on', $event->ask_for_all_attendees_info) !!}
                                Require details for each attendee?
                            </label>
                        </div>
                        <div class="help-block">
                            If checked, the buyer will be asked for details of each attendee; as opposed to just
                            himself.
                        </div>
                    </div>


                    <div class="form-group">
                        {!! Form::label('pre_order_display_message', 'Message to display to attendees before they complete their order.', array('class'=>'control-label ')) !!}

                        {!!  Form::textarea('pre_order_display_message', $event->pre_order_display_message, [
                            'class' => 'form-control',
                            'rows' => 4
                        ])  !!}
                        <div class="help-block">
                            This message will be displayed to attendees immediately before they finalize their order.
                        </div>

                    </div>
                    <div class="form-group">
                        {!! Form::label('post_order_display_message', 'Message to display to attendees before after they have completed their order.', array('class'=>'control-label ')) !!}

                        {!!  Form::textarea('post_order_display_message', $event->post_order_display_message, [
                            'class' => 'form-control',
                            'rows' => 4
                        ])  !!}
                        <div class="help-block">
                            This message will be displayed to attendees once they have successfully completed the
                            checkout process.
                        </div>
                    </div>

                    <div class="panel-footer mt15 text-right">
                        {!! Form::submit('Save Changes', ['class'=>"btn btn-success"]) !!}
                    </div>

                    {!! Form::close() !!}

                </div>

                <div class="tab-pane {{$tab == 'ticket_design' ? 'active' : ''}}" id="ticket_design">

                    {!! Form::model($event, array('url' => route('postEditEventTicketDesign', ['event_id' => $event->id]), 'class' => 'ajax ')) !!}

                    <div class="row">
                        <div class="col-md-6">
                            <h4>Ticket Design</h4>

                            <div class="form-group">
                                {!! Form::label('name', 'Ticket Border Color', ['class'=>'control-label required ']) !!}
                                {!!  Form::input('color', 'ticket_border_color', Input::old('ticket_border_color'),
                                                            [
                                                            'class'=>'form-control',
                                                            'placeholder'=>'#000000'
                                                            ])  !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('name', 'Ticket Background Color', ['class'=>'control-label required ']) !!}
                                {!!  Form::input('color', 'ticket_bg_color', Input::old('ticket_bg_color'),
                                                            [
                                                            'class'=>'form-control',
                                                            'placeholder'=>'#FFFFFF'
                                                            ])  !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('name', 'Ticket Text Color', ['class'=>'control-label required ']) !!}
                                {!!  Form::input('color', 'ticket_text_color', Input::old('ticket_text_color'),
                                                            [
                                                            'class'=>'form-control',
                                                            'placeholder'=>'#000000'
                                                            ])  !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('name', 'Ticket Sub Text Color', ['class'=>'control-label required ']) !!}
                                {!!  Form::input('color', 'ticket_sub_text_color', Input::old('ticket_border_color'),
                                                            [
                                                            'class'=>'form-control',
                                                            'placeholder'=>'#000000'
                                                            ])  !!}
                            </div>

                        </div>
                        <div class="col-md-6">
                            <h4>Ticket Preview</h4>
                            @include('ManageEvent.Partials.TicketDesignPreview')
                        </div>
                    </div>
                    <div class="panel-footer mt15 text-right">
                        {!! Form::submit('Save Changes', ['class'=>"btn btn-success"]) !!}
                    </div>

                    {!! Form::close() !!}

                </div>

                <div class="tab-pane {{$tab == 'embed' ? 'active' : ''}}" id="embed">

                    <div class="row">
                        <div class="col-md-6">
                            <h4>HTML Embed Code</h4>
                            <textarea rows="7" onfocus="this.select();" class="form-control">{{$event->embed_html_code}}</textarea>
                        </div>
                        <div class="col-md-6">
                            <h4>Instructions</h4>

                            <p>
                                Simply copy and paste the HTML provided onto your website where you would like the
                                widget to appear and the widget will appear.
                            </p>

                            <h5>
                                <b>Embed Preview</b>
                            </h5>

                            <div class="preview_embed" style="border:1px solid #ddd; padding: 5px;">
                                {!! $event->embed_html_code !!}
                            </div>

                        </div>
                    </div>

                </div>

            </div>
            <!--/ tab content -->
        </div>
    </div>
@stop


