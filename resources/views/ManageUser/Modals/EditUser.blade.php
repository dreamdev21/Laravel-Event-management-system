<div role="dialog"  class="modal fade" style="display: none;">
    {!! Form::model($user, array('url' => route('postEditUser'), 'class' => 'ajax closeModalAfter')) !!}
        <div class="modal-dialog account_settings">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="modal-title">
                        <i class="ico-user"></i>
                        {{ trans('manageuser.my-profile') }}</h3>
                </div>
                <div class="modal-body">
                    @if(!Auth::user()->first_name)
                        <div class="alert alert-info">
                            <b>
                            {{ trans('manageuser.welcome-to') }}{{config('attendize.app_name')}}!
                            </b><br>
                            {{  trans('manageuser.update-account-name') }}
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('first_name',  trans('common.first-name') , array('class'=>'control-label required')) !!}
                                {!!  Form::text('first_name', Input::old('first_name'),
                                            array(
                                            'class'=>'form-control'
                                            ))  !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('last_name',  trans('common.last-name') , array('class'=>'control-label required')) !!}
                                {!!  Form::text('last_name', Input::old('last_name'),
                                            array(
                                            'class'=>'form-control'
                                            ))  !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('email',  trans('common.email') , array('class'=>'control-label required')) !!}
                                {!!  Form::text('email', Input::old('email'),
                                            array(
                                            'class'=>'form-control '
                                            ))  !!}
                            </div>
                        </div>
                    </div>

                    <div class="row more-options">
                        <div class="col-md-12">

                            <div class="form-group">
                                {!! Form::label('password', trans('common.old-password'), array('class'=>'control-label')) !!}
                                {!!  Form::password('password',
                                            array(
                                            'class'=>'form-control'
                                            ))  !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('new_password', trans('common.new-password'), array('class'=>'control-label')) !!}
                                {!!  Form::password('new_password',
                                            array(
                                            'class'=>'form-control'
                                            ))  !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('new_password_confirmation', trans('common.confirm-new-password'), array('class'=>'control-label')) !!}
                                {!!  Form::password('new_password_confirmation',
                                            array(
                                            'class'=>'form-control'
                                            ))  !!}
                            </div>
                        </div>
                    </div>
                    <a data-show-less-text='Hide Change Password' href="javascript:void(0);" class="in-form-link show-more-options">
                        Change Password{{ trans('common.change-password') }}
                    </a>
                </div>
                <div class="modal-footer">
                   {!! Form::button(trans('common.cancel'), ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                   {!! Form::submit(trans('common.save-details'), ['class' => 'btn btn-success pull-right']) !!}
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</div>
