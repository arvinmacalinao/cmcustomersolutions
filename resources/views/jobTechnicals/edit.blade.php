@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Update Job Details</div>
                    <div class="panel-body">
                        {!! Form::model($tech_job, array( 'route' => array('jobtechnical.update', $tech_job->id), 'method' => 'put', 'class' => 'form-horizontal', 'id' => 'form_update_tech_job' )) !!}
                        
                        @include('jobTechnicals.form')
                        <div class="form-group">
                            <div class="col-md-7 col-md-offset-3">
                                <button type="submit" class="btn btn-primary" name="update_btn" value="update" id="update_btn">
                                    <i class="fa fa-btn fa-pencil-square-o"></i> Update
                                </button>
                                <button type="submit" class="btn btn-success" name="complete_btn" value="complete">
                                    <i class="fa fa-btn fa-check"></i> Complete
                                </button>
                                <button type="submit" class="btn btn-warning" name="pullout_btn" value="pullout">
                                    <i class="fa fa-btn fa-sign-out"></i> Pull Out
                                </button>
                                <a class="btn btn-danger" href="{{ route('jobtechnical.index') }}">
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