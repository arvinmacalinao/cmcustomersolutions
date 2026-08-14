@inject('param', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">{{ucwords(str_replace('_', ' ', $report_type))}}</h3>
				</div>
				<div class="panel-body">
					@if ($report_type == "branch_job_device_warranty_report")
						{!! Form::open(['method'=>'GET','url'=>route('report.list.branch.warranty'),'class'=>'form-horizontal','role'=>'search'])  !!}

						<div class="form-group col-md-6 ">
							{!! Form::label('year', 'Year', array('class' => 'control-label col-md-3')); !!}
							<div class="col-md-9">
								{!! Form::select('year', $param::getYear(), old('year'), ['class' => 'form-control', 'placeholder' => 'Report Year...', 'required']); !!}
				            </div>
				        </div>
						
						<div class="form-group col-md-6 ">
							{!! Form::label('company_id', 'Company', array('class' => 'control-label col-md-3')); !!}
							<div class="col-md-9">
								{!! Form::select('company_id', $companies, old('company_id'), ['class' => 'form-control', 'placeholder' => 'Branch Report...', 'required']); !!}
							</div>
						</div>

						<div class="form-group col-md-12">
				            <div class="col-md-2 col-md-offset-10">
				                <button type="submit" name="download_btn" value="download" class="btn btn-default-sm">
				                    <i class="fa fa-download" aria-hidden="true"></i> Download
				                </button>
				            </div>
				        </div>
						{!! Form::close() !!}
					@else
						{!! Form::open(['method'=>'GET','url'=>route('report.list.branch.total'),'class'=>'form-horizontal','role'=>'search'])  !!}
						<div class="form-group col-md-6 ">
							{!! Form::label('company_id', 'Company', array('class' => 'control-label col-md-3')); !!}
							<div class="col-md-9">
								{!! Form::select('company_id', $companies, old('company_id'), ['class' => 'form-control', 'placeholder' => 'Branch Report...', 'required']); !!}
							</div>
						</div>

						<div class="form-group col-md-6 ">
							<div class="col-md-2 col-md-offset-4">
								<button type="submit" name="download_btn" value="download" class="btn btn-default-sm">
				                    <i class="fa fa-download" aria-hidden="true"></i> Download
				                </button>
			                </div>
				        </div>
						{!! Form::close() !!}
					@endif
				</div>
			</div>

		</div>
	</div>	
</div>

@stop