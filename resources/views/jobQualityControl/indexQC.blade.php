@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')
<div class="content">
	<div class="row">
        <div class="col-md-12">

        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Job Assignment</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('jobqualitycontrol.index'),'class'=>'form-horizontal','role'=>'search'])  !!}
					<!-- <a href="{{ route('ewarranty.index') }}" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-plus"></span> Add</a> -->

					<div class="form-group col-md-6 ">
							{!! Form::label('imei', 'IMEI', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
			            	{!! Form::text('imei', old('imei'), array('class' => 'form-control', 'placeholder' => 'Device IMEI...')); !!}
			            </div>
			        </div>
					
					<div class="form-group col-md-6 ">
							{!! Form::label('code', 'Model Code', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('code', old('code'), array('class' => 'form-control', 'placeholder' => 'Model Code...')); !!}
						</div>
					</div>

					<div class="form-group col-md-12">
			            <div class="col-md-2 col-md-offset-10">
			                <button type="submit" class="btn btn-default-sm">
			                    <i class="fa fa-search" aria-hidden="true"></i> Search
			                </button>
			            </div>
			        </div>
					{!! Form::close() !!}
				</div>
			</div>

			{!! Form::open(['method'=>'POST', 'url'=>route('jobqualitycontrol.accept'), 'class'=>'form-horizontal', 'id'=>'form-tech-accept-job'])  !!}
			<div class="form-group col-md-12 ">
				<div class="col-md-2">
	            	<h4>Job List</h4>
	            </div>
	            <div class="col-md-8">
	            </div>
	            <div class="col-md-2" align="right">
	            	{!! Form::submit('Accept Job', array('class' => 'btn btn-primary', 'id' => 'submitBtn')); !!}
	            </div>
	        </div>
			<table class="table">
				<thead>
					<tr>
						<th style="width:30px">
							{!! Form::checkbox('checkAll', null, null, array('class' => 'form-control', 'id' => 'checkAll')); !!}
						</th>
						<th>Job No.</th>
						<th>IMEI</th>
						<th>Brand</th>
						<th>Model</th>
						<th>Level</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($qc_jobs as $index => $qc_job)
					<tr>
						<td style="width:30px">
							{!! Form::checkbox('job_qc_id[]', $qc_job->id, old('job_qc_id'), array('class' => 'form-control')); !!}
						</td>
						<td>{{ sprintf('JO%08d', $qc_job->technicalJob->job_id) }}</td>
						<td>{{ $qc_job->technicalJob->job->imei }}</td>
						<td>{{ $qc_job->technicalJob->job->device->inventory->model->brand->name }}</td>
						<td>{{ $qc_job->technicalJob->job->device->inventory->model->code }}</td>
						<td>{{ $qc_job->technicalJob->job->level->name }}</td>
						<td>{{ $globalVar::getQCJobStatus()[$qc_job->status] }}</td>
						<td>
							<select class = 'form-control job-action'>
								<option selected="selected" value>Pick a Task...</option>
								@if ( $qc_job->status == 'wip' )
								<option value="{{ route('jobqualitycontrol.edit', $qc_job->job_technical_id) }}" id="update-job">QC Job</option>
								@endif
								{{--<option value="{{ route('job.form.joborder', $tech_job->job_id) }}">Job Order Report</option>
								<option value="{{ route('job.log', $tech_job->job_id) }}">View Job Log</option>
								<option value="{{ route('job.cancel', $tech_job->job_id) }}" id="cancel-job">Cancel Job</option>--}}
							</select>
						</td>
					</tr>
				    @endforeach
				</tbody>
			</table>
			{!! $qc_jobs->appends(request()->query())->render() !!}
			
			{!! Form::close() !!}
		</div>
	</div>	
</div>
@stop

@section('scripts')
<script type="text/javascript">

	$('#checkAll').click(function () {    
		$('input:checkbox').prop('checked', this.checked);    
	});

	$("form#form-tech-accept-job").submit(function( event ) {
		checked = $("input[type=checkbox]:checked").length;

	    if( !checked ) {
	        //alert({!! trans('cdu.err_select_item', ['item' => 'job']) !!});
	        alert('You must select at least 1 job.');
	        return false;
	    }
	});

	$( "select.job-action" ).change(function() {
		if( $(this).children(":selected").attr("id") == 'update-job' ) {
			//alert( $(this).val() );
			location.href = $(this).val();
		} else {
			//location.href = $(this).val();
		}		
	});
</script>
@stop