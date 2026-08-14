@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="content">
@if(!$jobLogs->isEmpty())
<table class="table">
	<caption>Job {{ sprintf('JO%08d', $jobLogs[0]->job_id) }}</caption>
	<thead>
		<tr>
			<th>#</th>
			<th>Process</th>
			<th>Description</th>
			<th>Log By</th>
			<th>IP Address</th>
			<th>Date</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($jobLogs as $index => $log)
		<tr>
			<td>{{ ++$index }}</td>
			<td>{{ $globalVar::getJobProcessByID()[$log->process_id] }}</td>
			<td>{{ $log->description }}</td>
			<td>{{ $log->logBy->name }}</td>
			<td>{{ $log->ip_address }}</td>
			<td>{{ Carbon\Carbon::parse($log->created_at)->timezone('Asia/Manila')  }}</td>
		</tr>
	        
	    @endforeach
		
	</tbody>
</table>
@else
<p>There's no job log.</p>
@endif
	
</div>

@stop