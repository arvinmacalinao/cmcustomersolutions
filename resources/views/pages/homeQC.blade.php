@extends('layout')

@section('content')


<div class="content">
	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Dashboard</h1>
        </div>
    </div>
    
    <!-- Job QC Status -->
    @can('job_qc')
	<div class="row">
    <div class="col-sm-12">
    <div class="row">

        <div class="col-lg-4 col-md-8">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-tasks fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{ $total_new_job }}</div>
                            <div>New Job</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <a href="{{ route('jobqualitycontrol.index', ['status' => 'new']) }}">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        </a>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-check-circle fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{ $total_ongoing_job }}</div>
                            <div>Ongoing Job</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <a href="{{ route('jobqualitycontrol.index', ['status' => 'wip']) }}">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        </a>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-md-8">
            <div class="panel panel-red">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-exclamation-triangle fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{ $total_expire_job }}</div>
                            <div>Expired Job</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <a href="{{ route('jobqualitycontrol.index', ['status' => 'expire']) }}">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        </a>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>

    </div>
    </div>
    </div>
    @endcan
    <!-- /Job QC Status -->
    
</div>

@stop