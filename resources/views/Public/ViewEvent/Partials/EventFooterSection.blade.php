@push('footer')
    <div class="push"></div>
@endpush

<footer id="footer" class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                {{--Attendize is provided free of charge on the condition the below hyperlink is left in place.--}}
                {{--See https://github.com/Attendize/Attendize/blob/master/LICENSE for more information.--}}
                @include('Shared.Partials.PoweredBy')
            </div>
        </div>
    </div>
</footer>