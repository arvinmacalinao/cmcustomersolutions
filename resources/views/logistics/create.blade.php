@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Create a New Delivery Order</div>
                    <div class="panel-body">
                        {!! Form::open(array( 'route' => array('logistic.store'), 'method' => 'post', 'class' => 'form-horizontal', 'id'=>'form-create-logistic' )) !!}

                        @include('logistics.form')
                        
                        <div class="form-group">
                            <div class="col-md-3 col-md-offset-9">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-plus" aria-hidden="true"></i> Create
                                </button>
                                <a class="btn btn-danger" href="{{ route('logistic.index') }}"><i class="fa fa-btn fa-times"></i> Cancel</a>
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop