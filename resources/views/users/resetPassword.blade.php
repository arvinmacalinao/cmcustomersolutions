@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Change Passowrd for {{ $user->name }}</div>
                    <div class="panel-body">

                        {!! Form::open(['route' => 'user.password.reset', 
                                        'class' => 'form-horizontal', 
                                        'method' => 'patch', 
                                        'role' => 'form']) !!}
                                        
                            {!! Form::hidden('id', $user->id) !!}

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                {!! Form::label('password', 'New Password', ['class' => 'col-md-4 control-label']) !!}

                                <div class="col-md-6">
                                    {!! Form::password('password', ['class' => 'form-control', 'required' => 'required']) !!}

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label" for="password_confirmation">
                                    Confirm New Password
                                </label>

                                <div class="col-md-6">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required="required">

                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-btn fa-pencil-square-o"></i> Update Password
                                    </button>
                                </div>
                            </div>
                        
                        {!! Form::close() !!}

                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop