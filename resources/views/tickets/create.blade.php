@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Create a New Ticket</div>
                    <div class="panel-body">
                        {!! Form::open(array( 'route' => array('ticket.store'), 'method' => 'post', 'class' => 'form-horizontal' )) !!}

                        @include('tickets.form')
                        
                        <div class="form-group">
                            <div class="col-md-7 col-md-offset-3">
                                <button id="submitBtn" type="submit" class="btn btn-primary" onclick="saveImg()">
                                    <i class="fa fa-plus" aria-hidden="true"></i> Create
                                </button>
                                
                                <a class="btn btn-danger" href="{{ route('ticket.index') }}"><i class="fa fa-btn fa-times"></i> Cancel</a>
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop