@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Update Device Registration Details</div>
                    <div class="panel-body">
                        {!! Form::model($device, array( 'route' => array('device_registration.update', $device->imei), 'method' => 'put', 'class' => 'form-horizontal' )) !!}

                        @include('deviceRegistrations.editForm')

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-pencil-square-o"></i> Update
                                </button>
                                <a class="btn btn-danger" href="{{ route('device_registration.index') }}"><i class="fa fa-btn fa-times"></i> Cancel</a>
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop