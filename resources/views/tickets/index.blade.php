@inject('param', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Ticket Search Panel</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('ticket.index'),'class'=>'form-horizontal','role'=>'search'])  !!}

					<div class="form-group col-md-6 ">
							{!! Form::label('imei', 'IMEI', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
			            	{!! Form::text('imei', old('imei'), array('class' => 'form-control', 'placeholder' => 'Device IMEI...')); !!}
			            </div>
			        </div>
					
					<div class="form-group col-md-6 ">
							{!! Form::label('job_id', 'Job ID', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('job_id', old('job_id'), array('class' => 'form-control', 'placeholder' => 'Job ID ie: 1 instead of JO00000001')); !!}
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

			<table class="table">
				<caption>Ticket List</caption>
				<thead>
					<tr>
						<th>Ticket No.</th>
						<th>Job No.</th>
						<th>Type</th>
						<th>IMEI</th>
						<th>Company</th>
						<th>Customer Name</th>
						<th>Contact</th>
						<th width="20%">Description</th>
						<th>Log By</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($tickets as $index => $ticket)
					<tr>
						<td>{{ $ticket->id }}</td>
						<td>{{ sprintf('JO%08d', $ticket->job_id) }}</td>
						<td>{{ $param::getTicketType()[$ticket->type] }}</td>
						<td>{{ $ticket->job->imei }}</td>
						<td>{{ $ticket->company->company_name }}</td>
						<td>{{ $ticket->customer_name }}</td>
						<td>{{ $ticket->customer_contact }}</td>
						<td>{{ $ticket->description }}</td>
						<td>{{ $ticket->creator->name }}</td>
					</tr>
				    @endforeach
				</tbody>
			</table>

		</div>
	</div>	
</div>

{!! $tickets->appends(request()->query())->render() !!}

@stop