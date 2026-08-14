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
					@if ($report_type == "total_level_three_report")
					{!! Form::open(['method'=>'GET','url'=>route('report.list.critical.total'),'class'=>'form-horizontal']) !!}
					@else
					{!! Form::open(['method'=>'GET','url'=>route('report.list.critical.warranty'),'class'=>'form-horizontal']) !!}
					@endif
					<div class="form-group col-md-6 ">
						{!! Form::label('year', 'Report Year', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::select('year', $param::getYear(), old('year'), ['class' => 'form-control', 'placeholder' => 'Report Year...', 'required']); !!}
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
				</div>
			</div>

		</div>
	</div>	
</div>

@stop