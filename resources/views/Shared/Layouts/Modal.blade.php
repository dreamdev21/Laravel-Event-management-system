
<!--Currently not using a modal layout a modals variation to much in content.-->

<div id="@yield('modal-id')" class="modal fade " style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title text-success">
                    @yield('title')
                </h2>
            </div>
            <div class="modal-body">
                @yield('body')
            </div><!-- /.modal-content -->
            <div class="modal-footer">
                @yield('footer')
            </div>
        </div><!-- /.modal-dialog -->
    </div>
</div>