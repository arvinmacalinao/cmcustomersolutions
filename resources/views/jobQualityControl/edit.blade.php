@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">QC Tech Job Details</div>
                    <div class="panel-body">
                        {!! Form::model($job_tech, array( 'route' => array('jobqualitycontrol.update', $job_tech->qualityControl->id), 'method' => 'put', 'class' => 'form-horizontal')) !!}
                        
                        @include('jobQualityControl.form')
                        <div class="form-group">
                            <div class="col-md-7 col-md-offset-3">
                                <button type="submit" class="btn btn-success" name="pass_btn" value="pass">
                                    <i class="fa fa-btn fa-check"></i> Pass
                                </button>
                                <button type="submit" class="btn btn-warning" name="fail_btn" value="fail">
                                    <i class="fa fa-btn fa-exclamation-triangle"></i> Fail
                                </button>
                                <a class="btn btn-danger" href="{{ route('jobqualitycontrol.index') }}">
                                    <i class="fa fa-btn fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop