@inject('param', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Device Registration Search Panel</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('device_registration.index'),'class'=>'form-horizontal','role'=>'search'])  !!}
					<div class="form-group col-md-6 ">
							{!! Form::label('imei', 'IMEI', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('imei', old('imei'), array('class' => 'form-control', 'placeholder' => 'IMEI...')); !!}
						</div>
					</div>

					<div class="form-group col-md-6 ">
							{!! Form::label('model', 'Model', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
			            	{!! Form::text('model', old('model'), array('class' => 'form-control', 'placeholder' => 'Model Code...')); !!}
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
				<caption>Device Registration List</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>IMEI</th>
						<th>Model</th>
						<th>Customer Name</th>
						<th>Email</th>
						<th>Mobile Number</th>
						<th>Warranty Date</th>
						<th>Warranty Status</th>
						<th>Purchase Date</th>
						<th>Created By</th>
						<th>Created At</th>
						<th>Updated By</th>
						<th>Updated At</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($devices as $index => $device)
					<tr>
						<td>{{ (($devices->currentPage() - 1 ) * $devices->perPage()) + $index + 1 }}</td>
						<td>{{ $device->imei }}</td>
						<td>{{ $device->code }}</td>
						<td>{{ $device->name }}</td>
						<td>{{ $device->email }}</td>
						<td>{{ $device->mobile_number }}</td>
						<td>{{ $device->warranty_date }}</td>
						<td>{{ $param::getWarrantyStatus()[$device->warranty_status] }}</td>
						<td>{{ $device->pop_date }}</td>
						<td>{{ $device->creator }}</td>
						<td>{{ $device->created_at }}</td>
						<td>{{ $device->updater }}</td>
						<td>{{ $device->updated_at }}</td>
						<td>
							<a href="{{ route('device_registration.edit', $device->imei) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
						</td>
					</tr>
				        
				    @endforeach
					
				</tbody>
			</table>

		</div>
	</div>
</div>

{!! $devices->appends(request()->query())->render() !!}

@stop
