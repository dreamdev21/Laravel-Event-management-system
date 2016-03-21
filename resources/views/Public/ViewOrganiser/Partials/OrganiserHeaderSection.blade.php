<section id="intro" class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="organiser_logo">
                <div class="thumbnail">
                    <img src="{{$organiser->full_logo_path}}" />
                </div>
            </div>
            <h1>{{$organiser->name}}</h1>
            <div class="description">
                {!! $organiser->about !!}
            </div>
        </div>
    </div>
</section>
