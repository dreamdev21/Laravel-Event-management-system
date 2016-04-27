<style>
    .page-header {
        /*opacity: .1;*/
    }
</style>
<div class="col-lg-6 col-lg-offset-3">
    <div class="panel panel-minimal" style="margin-top:10%;">
        <div class="panel-body text-center">
            <i class="@yield('blankslate-icon-class')  fsize112"></i>
        </div>
        <div class="panel-body text-center">
            <h1 style="font-weight: 100;" class=" text-center fsize32 mb10 mt0">
                @yield('blankslate-title')
            </h1>
            <h5 style="font-size: 20px; font-weight: 100; line-height: 1.5em;"  class=" pa10 text-primary text-center ">
                @yield('blankslate-text')
            </h5>
            @yield('blankslate-body')
        </div>
    </div>
</div>