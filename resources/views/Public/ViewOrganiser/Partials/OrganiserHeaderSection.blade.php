@if(Auth::check())
    <section id="adminBar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <a class="btn btn-primary btn-xs" href="{{route('showOrganiserDashboard' , ['organiser_id' => $organiser->id])}}" >Organiser Dashboard</a>
                </div>
            </div>
    </section>
    @endif
<section id="intro" class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="organiser_logo">
                <div class="thumbnail">
                    <img src="{{$organiser->full_logo_path}}" />
                </div>
            </div>
            <h1>{{$organiser->name}}</h1>
            @if($organiser->about)
            <div class="description pa10">
                {!! $organiser->about !!}
            </div>
            @endif
        </div>
    </div>
</section>
