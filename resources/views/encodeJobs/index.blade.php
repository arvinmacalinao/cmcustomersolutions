@inject('param', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Job Search Panel</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('encode_job.index'),'class'=>'form-horizontal','role'=>'search'])  !!}

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
							{!! Form::select('limit', [30 => 30, 100 => 100, 200 => 200], old('limit'), ['class' => 'form-control', 'required']); !!}
						</div>
					</div>

					<div class="form-group col-md-6 ">
							{!! Form::label('company_id', 'Company', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::select('company_id', $companies, old('company_id'), ['class' => 'form-control', 'placeholder' => 'Select a Company...']); !!}
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

			@if( isset($encode_jobs) AND !$encode_jobs->isEmpty() )
				<table class="table">
					<caption>Job List</caption>
					<thead>
						<tr>
							<th>#</th>
							<th>Job No.</th>
							<th>Job Type</th>
							<th>IMEI</th>
							<th>Model Code</th>
							<th>Created On</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($encode_jobs as $index => $encode_job)
						<tr>
							<td>{{ (($encode_jobs->currentPage() - 1 ) * $encode_jobs->perPage()) + $index + 1 }}</td>
							<td>{{ sprintf('JO%08d', $encode_job->jobLogistic->job->id) }}</td>
							<td>{{ $param::getJobType()[$encode_job->jobLogistic->job->job_type] }}</td>
							<td>{{ $encode_job->jobLogistic->job->imei }}</td>
							<td>{{ $encode_job->jobLogistic->job->device->inventory->model->code }}</td>
							<td>{{ $encode_job->created_at }}</td>
							<td>
								@if( $encode_job->status == null ) 
								<a href="{{ route('encode_job.edit', $encode_job->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
								&nbsp;&nbsp;
								<a href="{{ route('encode_job.approve', [$encode_job->id, 1]) }}"><i class="fa fa-check" aria-hidden="true"></i></a>
								&nbsp;&nbsp;
								<a class="encode_deny" href="{{ route('encode_job.approve', [$encode_job->id, 0]) }}"><i class="fa fa-times" aria-hidden="true"></i></a>
								@endif
							</td>
						</tr>
					    @endforeach
					</tbody>
				</table>
				
				{!! $encode_jobs->appends(request()->query())->render() !!}

			@else
				<p>There's no device available for encoding.</p>
			@endif
		</div>
	</div>	
</div>

@stop

@section('scripts')
<script type="text/javascript">

$("a.encode_deny ").click(function() {
	var encode_job_deny = prompt("{{trans('cdu.confirm_encode_job_deny')}}");

	if (encode_job_deny === null) {
		return false; //break out of the function early
	}

	var compiledURL = $(this).attr('href') + '?description=' + encodeURI(encode_job_deny);
	//alert(compiledURL);

	location.href = compiledURL;
	return false;
});

</script>
@stop