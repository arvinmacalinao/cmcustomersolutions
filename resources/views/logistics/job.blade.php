@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="content">
@if( !is_null($logistic) )
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Logistic Details</h3>
		</div>
		<div class="panel-body">
			<div class="form-group col-md-6 ">
				<div class="col-md-4">
					Logistic ID:
				</div>
				<div class="col-md-8">
					{{ $logistic->id }}
				</div>
			</div>

			<div class="form-group col-md-6 ">
				<div class="col-md-4">
					Tracking No.:
				</div>
				<div class="col-md-8">
	            	{{ sprintf('DO%08d', $logistic->id) }}
	            </div>
	        </div>
		</div>

		<div class="panel-body">
			<div class="form-group col-md-6 ">
				<div class="col-md-4">
					Created By:
				</div>
				<div class="col-md-8">
					{{ $logistic->creator->name }}
				</div>
			</div>

			<div class="form-group col-md-6 ">
				<div class="col-md-4">
					Company:
				</div>
				<div class="col-md-8">
	            	{{ $logistic->routeFrom->company_name }}
	            </div>
	        </div>
		</div>
	</div>

	<table class="table">
		<caption>Job List</caption>
		<thead>
			<tr>
				<th>#</th>
				<th>Job No.</th>
				<th>IMEI</th>
				<th>Device</th>
				<th>Job Type</th>
				<th>Created At</th>
			</tr>
		</thead>
		<tbody>
			@forelse ($logistic->jobs as $index => $item)
			<tr>
				<td>{{ ++$index }}</td>
				<td>{{ sprintf('JO%08d', $item->job_id) }}</td>
				<td>{{ $item->job->imei }}</td>
				<td>{{ $item->job->device->inventory->model->name }}</td>
				<td>{{ $globalVar->getJobType()[$item->job->job_type] }}</td>
				<td>{{ $item->job->created_at }}</td>
			</tr>
			@empty
			<p>There's no job included in this logistic.</p>
		    @endforelse
		</tbody>
	</table>
@else
	<p>Logistic record not found.</p>
@endif
</div>

@stop