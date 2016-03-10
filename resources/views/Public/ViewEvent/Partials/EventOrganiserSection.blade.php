<section id="organiser" class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="event_organiser_details">
                <div class="logo">
                    <img alt="{{$event->organiser->name}}" src="{{asset($event->organiser->full_logo_path)}}"/>
                </div>
                <h3>
                    {{$event->organiser->name}}
                </h3>

                <p>
                    {{nl2br($event->organiser->about)}}
                </p>
                <p>
                    @if($event->organiser->facebook)
                        <a href="https://fb.com/{{$event->organiser->facebook}}" class="btn btn-facebook">
                            <i class="ico-facebook"></i>&nbsp; Facebook
                        </a>
                    @endif
                        @if($event->organiser->twitter)
                            <a href="https://twitter.com/{{$event->organiser->twitter}}" class="btn btn-twitter">
                                <i class="ico-twitter"></i>&nbsp; Twitter
                            </a>
                        @endif
                    <button onclick="$(function(){ $('.contact_form').slideToggle(); });" type="button" class="btn btn-primary">
                        <i class="ico-envelop"></i>&nbsp; Contact
                    </button>
                </p>
                <div class="contact_form well well-sm">
                    {!! Form::open(array('url' => route('postContactOrganiser', array('event_id' => $event->id)), 'class' => 'reset ajax')) !!}
                    <h3>Contact <i>{{$event->organiser->name}}</i></h3>
                    <div class="form-group">
                        {!! Form::label('Your Name') !!}
                        {!! Form::text('name', null,
                            array('required',
                                  'class'=>'form-control',
                                  'placeholder'=>'Your name')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('Your E-mail Address') !!}
                        {!! Form::text('email', null,
                            array('required',
                                  'class'=>'form-control',
                                  'placeholder'=>'Your e-mail address')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('Your Message') !!}
                        {!! Form::textarea('message', null,
                            array('required',
                                  'class'=>'form-control',
                                  'placeholder'=>'Your message')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::submit('Send Message',
                          array('class'=>'btn btn-primary')) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>

