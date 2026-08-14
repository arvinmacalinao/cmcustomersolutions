@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Update Special Case</div>
                    <div class="panel-body">
                        {!! Form::model($case, array( 'route' => array('special_case.update', $case->id), 'method' => 'put', 'class' => 'form-horizontal' )) !!}
                        
                        @include('specialCases.form')
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" id="submitBtn" class="btn btn-primary" name="pass_btn" value="pass">
                                    <i class="fa fa-btn fa-pencil-square-o"></i> Approve
                                </button>
                                <button type="submit" class="btn btn-warning" name="fail_btn" value="fail">
                                    <i class="fa fa-btn fa-sign-out"></i> Reject
                                </button>
                                <a class="btn btn-danger" href="{{ route('special_case.index') }}"><i class="fa fa-btn fa-times"></i> Cancel</a>
                            </div>
                        </div>
                        
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop