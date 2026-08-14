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
					<form method="GET" action="{{route('jobtechnical.index')}}" accept-charset="UTF-8" class="form-horizontal" role="search">
					{{-- {!! Form::open(['method'=>'GET','url'=>route('jobtechnical.index'),'class'=>'form-horizontal','role'=>'search'])  !!} --}}

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
					<!--Requested by Alvin Dela Cruz to remove Feb 14, 2019 c/o by Mary Anne Garalde--><div class="form-group col-md-6 ">
							{!! Form::label('limit', 'Record Size', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::select('limit', [50 => 50, 100 => 100, 200 => 200, 500 => 500, 1000 => 1000, 2000 => 2000, 3000 => 3000], old('limit'), ['class' => 'form-control', 'required']); !!}
						</div>
					</div>

					<div class="form-group col-md-12">
			            <div class="col-md-2 col-md-offset-10">
			                <button type="submit" class="btn btn-default-sm">
			                    <i class="fa fa-search" aria-hidden="true"></i> Search
			                </button>
			            </div>
			        </div>
			    	</form>
					{{-- {!! Form::close() !!} --}}
				</div>
			</div>

			{!! Form::open(['method'=>'POST', 'url'=>route('jobtechnical.store'), 'class'=>'form-horizontal', 'id'=>'form-assign-technician'])  !!}
			<table class="table">
				<caption>Job List</caption>
				<thead>
					<tr>
						<th style="width:30px">
							{!! Form::checkbox('checkAll', null, null, array('class' => 'form-control', 'id' => 'checkAll')); !!}
						</th>
						<th>Job No.</th>
						<th>Job Type</th>
						<th>IMEI</th>
						<th>Brand</th>
						<th>Model</th>
						<th>Contact Name</th>
						<th>Contact No.</th>
						<th>Status</th>
						<!--Requested by Alvin Dela Cruz to remove Feb 15, 2019 c/o by Mary Anne Garalde
							<th>Action</th>-->
					</tr>
				</thead>
				<tbody>
					@foreach ($jobs as $index => $job)
					<tr>
						<td style="width:30px">
							{!! Form::checkbox('job_id[]', $job->id, old('job_id'), array('class' => 'form-control')); !!}
						</td>
						<td>{{ sprintf('JO%08d', $job->id) }}</td>
						<td>{{ $globalVar::getJobType()[$job->job_type] }}</td>
						<td>{{ $job->imei }}</td>
						<td>{{ $job->device->inventory->model->brand->name }}</td>
						<td>{{ $job->device->inventory->model->code }}</td>
						<td>{{ $job->contact_name }}</td>
						<td>{{ $job->contact_number }}</td>
						<td>{{ $job->status->name }}</td>
						<!--Requested by Alvin Dela Cruz to remove Feb 5, 2019 c/o by Mary Anne Garalde<td>
							<select class = 'form-control job-action'>
								<option selected="selected" value>Pick a Task...</option>
								<option value="{{ route('job.form.acknowledgement', $job->id) }}">Print Acknowledge Form</option>
								<option value="{{ route('job.form.joborder', $job->id) }}">Job Order Report</option>
								<option value="{{ route('job.log', $job->id) }}">View Job Log</option>
								<option value="{{ route('job.cancel', $job->id) }}" id="cancel-job">Cancel Job</option>
							</select>
						</td>-->
					</tr>
				    @endforeach
				</tbody>
			</table>
			{!! $jobs->appends(request()->query())->render() !!}

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Technician Assignment</h3>
				</div>
				<div class="panel-body">
					<div class="form-group col-md-12 ">
						<div class="col-md-2">
							{!! Form::label('technician_id', 'Technician', array('class' => 'control-label')); !!}
						</div>
						<div class="col-md-8">
							{!! Form::select('technician_id', $technicians, null, ['class' => 'form-control', 'required']); !!}
						</div>
						<div class="col-md-2">
			            	{!! Form::submit('Assign Job', array('class' => 'btn btn-primary', 'id' => 'submitBtn')); !!}
			            </div>
			        </div>
				</div>
			</div>
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

	$('#submitBtn').click(function() {
	        $(this).attr('disabled', 'disabled');
	        $('#form-assign-technician').submit();
            return true;
	});

	$( "select.job-action" ).change(function() {
		if( $(this).children(":selected").attr("id") == 'cancel-job' ) {
			var job_cancellation = prompt("{{trans('validation.confirm_job_cancellation')}}");

			if (job_cancellation === null) {
				return; //break out of the function early
			}
			//alert( $(this).val() + '?remark=' + encodeURI(job_cancellation) );
			location.href = $(this).val() + '?remark=' + encodeURI(job_cancellation);
		} else {
			location.href = $(this).val();
		}		
	});
	
	$("form#form-assign-technician").submit(function( event ) {
		checked = $("input[type=checkbox]:checked").length;

	    //alert('No. of checkbox selected ' + checked);

	    if( !checked ) {
	        //alert({!! trans('cdu.err_select_item', ['item' => 'job']) !!});
	        alert('You must select at least 1 job.');
	        return false;
	    }
	});
</script>
@stop