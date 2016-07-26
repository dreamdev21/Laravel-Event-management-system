<footer id="footer" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                {{--Attendize is provided free of charge on the condition the below hyperlink is left in place.--}}
                {{--See https://github.com/Attendize/Attendize/blob/master/LICENSE for more information.--}}
                @include('Shared.Partials.PoweredBy')

                @if(Auth::user()->account_id === $organiser->account_id)
                    &bull;
                    <a class="adminLink"
                       href="{{route('showOrganiserDashboard' , ['organiser_id' => $organiser->id])}}">Organiser
                        Dashboard</a>
                @endif
            </div>
        </div>
    </div>
</footer>
