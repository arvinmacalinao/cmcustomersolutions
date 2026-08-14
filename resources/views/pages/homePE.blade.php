@extends('layout')

@section('content')


<div class="content">
	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Dashboard</h1>
        </div>
    </div>
    
    <!-- Encode Job Status -->
    @can('encode_job')
	<div class="row">
    <div class="col-sm-12">
    <div class="row">

        <div class="col-lg-6 col-md-12">
            <div class="panel panel-green">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-tasks fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{ $total_encode_job }}</div>
                            <div>Encode Job</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <a href="{{ route('encode_job.index') }}">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        </a>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="panel panel-red">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-check-circle fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{ $total_expire_job }}</div>
                            <div>Expire Job</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <a href="{{ route('encode_job.index') }}">
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
    <!-- /Encode Job Status -->
    
</div>

@stop