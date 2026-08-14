@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')
<div class="content">
	<div class="row">
        <div class="col-md-12">

        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Quality Controller Assignment</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('jobqualitycontrol.index'),'class'=>'form-horizontal','role'=>'search'])  !!}

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
					<div class="form-group col-md-6 ">
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
					{!! Form::close() !!}
				</div>
			</div>

			{!! Form::open(['method'=>'POST', 'url'=>route('jobqualitycontrol.store'), 'class'=>'form-horizontal', 'id'=>'form-assign-qc'])  !!}
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
					</tr>
				</thead>
				<tbody>
					@foreach ($tech_jobs as $index => $tech_job)
					<tr>
						<td style="width:30px">
							{!! Form::checkbox('technical_job_id[]', $tech_job->id, old('technical_job_id'), array('class' => 'form-control')); !!}
						</td>
						<td>{{ sprintf('JO%08d', $tech_job->job->id) }}</td>
						<td>{{ $globalVar::getJobType()[$tech_job->job->job_type] }}</td>
						<td>{{ $tech_job->job->imei }}</td>
						<td>{{ $tech_job->job->device->inventory->model->brand->name }}</td>
						<td>{{ $tech_job->job->device->inventory->model->code }}</td>
						<td>{{ $tech_job->job->contact_name }}</td>
						<td>{{ $tech_job->job->contact_number }}</td>
						<td>{{ $tech_job->job->status->name }}</td>
					</tr>
				    @endforeach
				</tbody>
			</table>
			
			{!! $tech_jobs->appends(request()->query())->render() !!}

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Quality Controller Assignment</h3>
				</div>
				<div class="panel-body">
					<div class="form-group col-md-12 ">
						<div class="col-md-2">
							{!! Form::label('qc_id', 'Quality Controller', array('class' => 'control-label')); !!}
						</div>
						<div class="col-md-8">
							{!! Form::select('qc_id', $qc, null, ['class' => 'form-control', 'required']); !!}
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

	
	$("form#form-assign-qc").submit(function( event ) {
		checked = $("input[type=checkbox]:checked").length;

	    if( !checked ) {
	        alert('You must select at least 1 job for quality control.');
	        return false;
	    }
	});
</script>
@stop