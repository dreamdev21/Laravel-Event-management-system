<style>
    .organiser_logo {
        max-width: 150px;
        margin: 0 auto;
    }

    .organiser_logo .thumbnail {
        background-color: transparent;
        border: none;
    }

    #intro  {

        margin-top: 20px;
        color:#fff !important;
        background-color: #AF5050 !important;
    }
</style>
<section id="intro" class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="organiser_logo">
                <div class="thumbnail">
                    <img src="{{$organiser->full_logo_path}}" />
                </div>
            </div>
            <h1>{{$organiser->name}}</h1>
        </div>
    </div>
</section>
