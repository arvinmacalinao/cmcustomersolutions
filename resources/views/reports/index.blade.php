@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

			<table class="table">
				<caption>{{$title}}</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>{{$title}}</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($reports as $index => $report)
					<tr>
						<td>{{ (($reports->currentPage() - 1 ) * $reports->perPage()) + $index + 1 }}</td>
						<td>{{$report->name}}</td>
						<td>
							@if( $report->status )
								pass
							@else
								fail
							@endif
						</td>
						<td>
							<a href="{{ route('report.download', $report->id) }}">
								<i class="fa fa-download" aria-hidden="true"></i>
							</a>
						</td>
					</tr>
				    @endforeach					
				</tbody>
			</table>

        </div>
    </div>
</div>

{!! $reports->render() !!}

@stop

