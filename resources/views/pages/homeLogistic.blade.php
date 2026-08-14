@extends('layout')

@section('content')


<div class="content">
	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Dashboard</h1>
        </div>
    </div>
    
    <!-- Logistic Status -->
    @can('logistic_mgmt')
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
                            <div class="huge">{{ $total_incoming_logistic }}</div>
                            <div>Total Incoming Shipment</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <a href="{{ route('logistic.index', ['status' => 'incoming']) }}">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        </a>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-check-circle fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge">{{ $total_ready_device }}</div>
                            <div>Total Device Ready for Shipment</div>
                        </div>
                    </div>
                </div>
                <a href="#">
                    <div class="panel-footer">
                        <a href="{{ route('logistic.create') }}">
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
    <!-- /Logistic Status -->

</div>

@stop