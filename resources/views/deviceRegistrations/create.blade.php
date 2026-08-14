@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Register Customer Device</div>
                    <div class="panel-body">
                        {!! Form::open(array( 'route' => array('device_registration.store'), 'method' => 'post', 'class' => 'form-horizontal' )) !!}
                        
                        @include('deviceRegistrations.form')

                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-plus" aria-hidden="true"></i> Register
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