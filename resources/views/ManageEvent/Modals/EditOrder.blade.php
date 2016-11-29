<div role="dialog"  class="modal fade" style="display: none;">
    <style>
        .well.nopad {
            padding: 0px;
        }
        .modal-body .row{
            margin-top:2rem;
        }
    </style>

    <div class="modal-dialog">
        <div class="modal-content">
            {!! Form::open(array('url' => route('postOrderEdit', array('order_id' => $order->id)), 'class' => 'ajax reset closeModalAfter')) !!}
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-cart"></i>
                    Order: <b>{{$order->order_reference}}</b></h3>
            </div>
            <div class="modal-body">
                <h3>Order Details</h3>
                <div class="row">
                    <div class="col-xs-12">
                        <label for="first_name" class="form-control-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ $order->first_name }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <label for="last_name" class="form-control-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ $order->last_name }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <label for="email" class="form-control-label">Email</label>
                        <input type="text" name="email" class="form-control" value="{{ $order->email }}">
                    </div>
                </div>
            </div> <!-- /end modal body-->

            <div class="modal-footer">
                {!! Form::button('Close', ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                {!! Form::submit('Update Order', ['class'=>"btn btn-success"]) !!}
            </div>
            {!! Form::close() !!}
        </div><!-- /end modal content-->
    </div>
</div>
