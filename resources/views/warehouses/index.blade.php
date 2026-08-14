@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Warehouse Search Panel</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('warehouse.index'),'class'=>'form-horizontal','role'=>'search'])  !!}

					<div class="form-group col-md-6 ">
							{!! Form::label('warehouse_name', 'Warehouse', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
			            	{!! Form::text('warehouse_name', old('warehouse_name'), array('class' => 'form-control', 'placeholder' => 'Name of Warehouse...')); !!}
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

			<table class="table">
				<caption>Warehouse List</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Company</th>
						<th>State</th>
						<th>Created By</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($warehouses as $index => $warehouse)
					<tr>
						<td>{{ (($warehouses->currentPage() - 1 ) * $warehouses->perPage()) + $index + 1 }}</td>
						<td>{{ $warehouse->name }}</td>
						<td>{{ $warehouse->company->company_name }}</td>
						<td>{{ $warehouse->state->state_name }}</td>
						<td>{{ $warehouse->creator->name }}</td>
						<td>
							<a href="{{ route('warehouse.edit', $warehouse->id) }}">
								<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
							</a>
							&nbsp;&nbsp;

							@can('delete_warehouse')
								{!! Form::model($warehouse, array('method' => 'delete', 
																'route' => ['warehouse.destroy', $warehouse->id], 
																'id' => 'delete_warehouse_'.$warehouse->id, 
																'style' => 'display:inline')) !!}
								<i id="{{$warehouse->id}}" class="fa fa-times"></i>
				                {!! Form::close() !!}
			                @endcan
						</td>
					</tr>
				    @endforeach
				</tbody>
			</table>

		</div>
	</div>	
</div>

{!! $warehouses->appends(request()->query())->render() !!}

@stop

@section('scripts')
<script type="text/javascript">
	$( "i.fa.fa-times" ).click(function() {
		var record_removal = confirm("{{trans('validation.confirm_delete', ['attribute' => 'warehouse'])}}");

		if (record_removal == true) {
			//alert('delete_inventory_'+this.id);
			document.getElementById('delete_warehouse_'+this.id).submit();
		} else {
			return false;
		}
	});
</script>
@stop