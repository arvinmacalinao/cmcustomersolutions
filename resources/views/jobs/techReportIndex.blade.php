@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

			<table class="table">
				<caption>Tech Job Reports</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>Tech Job ID</th>
						<th>Technician</th>
						<th>Remarks</th>
						<th>Assign Date</th>
						<th>Completion Date</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($reports as $index => $report)
					<tr>
						<td>{{ $index + 1 }}</td>
						<td>{{ sprintf('JB%08d', $report->id) }}</td>
						{{--<td>{{ $globalVar::getJobType()[$job->job_type] }}</td>--}}
						<td>{{ $report->technician->name }}</td>
						<td>
							@if($report->remark)
								{{ $report->remark }}, 
							@endif
							@if($report->remarks)
					            @foreach($report->remarks as $remark)
									@if ($remark == $report->remarks->last())
									{{ $remark->name }}
									@else
									{{ $remark->name }}, 
									@endif
					            @endforeach
				            @else
				            	-
				            @endif
						</td>
						<td>{{ $report->created_at }}</td>
						<td>{{ $report->completion_date }}</td>
						<td>
							<a href="{{ route('jobtechnical.form.technical', $report->id) }}" target="_blank">
								<i class="fa fa-file-pdf-o" aria-hidden="true"></i>
							</a>
						</td>
					</tr>
				    @endforeach
				</tbody>
			</table>

		</div>
	</div>	
</div>

@stop