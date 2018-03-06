<section id="organiser" class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="event_organiser_details" property="organizer" typeof="Organization">
                <div class="logo">
                    <img alt="{{$event->organiser->name}}" src="{{asset($event->organiser->full_logo_path)}}" property="logo">
                </div>
                    @if($event->organiser->enable_organiser_page)
                    <a href="{{route('showOrganiserHome', [$event->organiser->id, Str::slug($event->organiser->name)])}}" title="Organiser Page">
                        {{$event->organiser->name}}
                    </a>
                    @else
                        {{$event->organiser->name}}
                    @endif
                </h3>

                <p property="description">
                    {!! nl2br($event->organiser->about)!!}
                </p>
                <p>
                    @if($event->organiser->facebook)
                        <a property="sameAs" href="https://fb.com/{{$event->organiser->facebook}}" class="btn btn-facebook">
                            <i class="ico-facebook"></i>&nbsp; Facebook
                        </a>
                    @endif
                        @if($event->organiser->twitter)
                            <a property="sameAs" href="https://twitter.com/{{$event->organiser->twitter}}" class="btn btn-twitter">
                                <i class="ico-twitter"></i>&nbsp; Twitter
                            </a>
                        @endif
                    <button onclick="$(function(){ $('.contact_form').slideToggle(); });" type="button" class="btn btn-primary">
                        <i class="ico-envelop"></i>&nbsp; {{ trans('viewevent.contact') }}
                    </button>
                </p>
                <div class="contact_form well well-sm">
                    {!! Form::open(array('url' => route('postContactOrganiser', array('event_id' => $event->id)), 'class' => 'reset ajax')) !!}
                    <h3>Contact <i>{{$event->organiser->name}}</i></h3>
                    <div class="form-group">
                        {!! Form::label(trans('common.your-name')) !!}
                        {!! Form::text('name', null,
                            array('required',
                                  'class'=>'form-control',
                                  'placeholder'=>trans('common.your-name'))) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label(trans('common.your-email')) !!}
                        {!! Form::text('email', null,
                            array('required',
                                  'class'=>'form-control',
                                  'placeholder'=>trans('common.your-email'))) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label(trans('common.your-message')) !!}
                        {!! Form::textarea('message', null,
                            array('required',
                                  'class'=>'form-control',
                                  'placeholder'=>trans('common.your-message'))) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit(trans('common.send-message'),
                          array('class'=>'btn btn-primary')) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>

