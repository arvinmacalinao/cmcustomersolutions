@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Inventory Search Panel</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('warehouse.inventory'),'class'=>'form-horizontal','role'=>'search'])  !!}

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
				<caption>Inventory List</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>Job No.</th>
						<th>Model</th>
						<th>IMEI</th>
						<th>Location</th>
						<th>Store By</th>
						<th>Store At</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($inventories as $index => $inventory)
					<tr>
						<td>{{ (($inventories->currentPage() - 1 ) * $inventories->perPage()) + $index + 1 }}</td>
						<td>{{ sprintf('JO%08d', $inventory->job_id) }}</td>
						<td>{{ $inventory->job->device->inventory->model->name }}</td>
						<td>{{ $inventory->job->imei }}</td>
						<td>{{ $inventory->warehouse->name }}</td>
						<td>{{ $inventory->creator->name }}</td>
						<td>{{ $inventory->created_at }}</td>
					</tr>
				    @endforeach
				</tbody>
			</table>

		</div>
	</div>	
</div>

{!! $inventories->appends(request()->query())->render() !!}

@stop