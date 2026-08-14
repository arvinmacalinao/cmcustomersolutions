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
					@if ($report_type == "total_model_defect_report")
						{!! Form::open(['method'=>'GET','url'=>route('report.defect.total'),'class'=>'form-horizontal']) !!}
						<div class="form-group col-md-6 ">
							{!! Form::label('year', 'Report Year', array('class' => 'control-label col-md-3')); !!}
							<div class="col-md-9">
								{!! Form::select('year', $param::getYear(), old('year'), ['class' => 'form-control', 'placeholder' => 'Report Year...', 'required']); !!}
				            </div>
				        </div>

						<div class="form-group col-md-6 ">
							{!! Form::label('month', 'Report Month', array('class' => 'control-label col-md-3')); !!}
							<div class="col-md-9">
								{!! Form::select('month', $param::getMonth(), old('month'), ['class' => 'form-control', 'placeholder' => 'Report Year...', 'required']); !!}
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
					@elseif ($report_type == "detailed_model_defect_report")
						{!! Form::open(['method'=>'GET','url'=>route('report.defect.details'),'class'=>'form-horizontal']) !!}
						<div class="form-group col-md-6 ">
							{!! Form::label('month', 'Month', array('class' => 'control-label col-md-3')); !!}
							<div class="col-md-9">
								{!! Form::select('month', $param::getMonth(), old('month'), ['class' => 'form-control', 'placeholder' => 'Report Month...', 'required']); !!}
							</div>
						</div>

						<div class="form-group col-md-6 ">
							{!! Form::label('year', 'Year', array('class' => 'control-label col-md-3')); !!}
							<div class="col-md-9">
								{!! Form::select('year', $param::getYear(), old('year'), ['class' => 'form-control', 'placeholder' => 'Report Year...', 'required']); !!}
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
					@elseif ($report_type == "ticketing_report")
						{!! Form::open(['method'=>'GET','url'=>route('report.ticket'),'class'=>'form-horizontal']) !!}
						<div class="form-group col-md-6 ">
							{!! Form::label('month', 'Month', array('class' => 'control-label col-md-3')); !!}
							<div class="col-md-9">
								{!! Form::select('month', $param::getMonth(), old('month'), ['class' => 'form-control', 'placeholder' => 'Report Month...', 'required']); !!}
							</div>
						</div>

						<div class="form-group col-md-6 ">
							{!! Form::label('year', 'Year', array('class' => 'control-label col-md-3')); !!}
							<div class="col-md-9">
								{!! Form::select('year', $param::getYear(), old('year'), ['class' => 'form-control', 'placeholder' => 'Report Year...', 'required']); !!}
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
					@elseif ($report_type == "pending_report")
						{!! Form::open(['method'=>'GET','url'=>route('report.pending'),'class'=>'form-horizontal']) !!}
						<div class="form-group col-md-6 ">
							{!! Form::label('date', 'Date', array('class' => 'control-label col-md-3')); !!}
							<div class="col-md-9">
								{!! Form::text('date', old('date'), array('class' => 'form-control', 'placeholder' => 'YYYY-MM-DD')); !!}
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
					@else
						{!! Form::open(['method'=>'GET','url'=>route('report.csr.performance'),'class'=>'form-horizontal']) !!}
						<div class="form-group col-md-6 ">
							{!! Form::label('month', 'Month', array('class' => 'control-label col-md-3')); !!}
							<div class="col-md-9">
								{!! Form::select('month', $param::getMonth(), old('month'), ['class' => 'form-control', 'placeholder' => 'Report Month...', 'required']); !!}
							</div>
						</div>

						<div class="form-group col-md-6 ">
							{!! Form::label('year', 'Year', array('class' => 'control-label col-md-3')); !!}
							<div class="col-md-9">
								{!! Form::select('year', $param::getYear(), old('year'), ['class' => 'form-control', 'placeholder' => 'Report Year...', 'required']); !!}
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
					@endif
					
				</div>
			</div>

		</div>
	</div>	
</div>

@stop

@section('scripts')
<script type="text/javascript">

	// Date feature
    $( function() {
        var date = $('#date').val();

        $( "#date" ).datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "-5:+0",
            dateFormat: 'yy-mm-dd',
            setDate: date,
        });
    } );
</script>
@stop