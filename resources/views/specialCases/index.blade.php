@inject('param', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

			<table class="table">
				<caption>Special Case List</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>Job No.</th>
						<th>Faulty Device</th>
						<th>Faulty IMEI</th>
						<th>New Device</th>
						<th>New IMEI</th>
						<th>Status</th>
						@can('approve_special_case')
						<th>Action</th>
						@endcan
					</tr>
				</thead>
				<tbody>
					@foreach ($special_cases as $index => $case)
					<tr>
						<td>{{ (($special_cases->currentPage() - 1 ) * $special_cases->perPage()) + $index + 1 }}</td>
						<td>{{ sprintf('JO%08d', $case->job_id) }}</td>
						<td>{{ $case->serviceDevice->model->name }}</td>
						<td>{{ $case->old_imei }}</td>
						@if( $case->new_imei )
						<td>{{ $case->claimDevice->model->name }}</td>
						<td>{{ $case->new_imei }}</td>
						@else
						<td>-</td>
						<td>-</td>
						@endif
						<td>{{ $param::getSpecialCaseStatus()[$case->status] }}</td>
						@can('approve_special_case')
						<td>
							@if($case->status == 1)
							<a href="{{ route('special_case.edit', $case->id) }}">
								<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
							</a>
							&nbsp;&nbsp;
							@endif
						</td>
						@endcan
					</tr>
				    @endforeach
				</tbody>
			</table>

		</div>
	</div>	
</div>

@stop