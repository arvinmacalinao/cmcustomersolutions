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
					{!! Form::open(['method'=>'GET','url'=>route('device_inventory.index'),'class'=>'form-horizontal','role'=>'search'])  !!}
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
				<caption>Device Inventory List</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>Model</th>
						<th>IMEI</th>
						<th>Color</th>
						<th>Created By</th>
						<th>Created At</th>
						<th>Updated By</th>
						<th>Updated At</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($inventories as $index => $inventory)
					<tr>
						<td>{{ (($inventories->currentPage() - 1 ) * $inventories->perPage()) + $index + 1 }}</td>
						<td>{{ $inventory->model->code }}</td>
						<td>{{ $inventory->imei }}</td>
						<td>{{ $inventory->color }}</td>
						<td>{{ $inventory->creator->name }}</td>
						<td>{{ $inventory->created_at }}</td>
						<td>
							@if($inventory->updater)
								{{ $inventory->updater->name }}
							@else
								-
							@endif
						</td>
						<td>{{ $inventory->updated_at }}</td>
						<td>
							<a href="{{ route('device_inventory.edit', $inventory->imei) }}">
								<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
							</a>
							&nbsp;&nbsp;
							{{-- @can('manage_inventory')
							{!! Form::model($inventory, array('method' => 'delete', 'route' => ['device_inventory.destroy', $inventory->imei], 'id' => 'delete_inventory_'.$inventory->imei, 'style' => 'display:inline')) !!}
			                <i id="{{$inventory->imei}}" class="fa fa-times"></i>
			                {!! Form::close() !!}
			                @endcan --}}
						</td>
					</tr>
				        
				    @endforeach
					
				</tbody>
			</table>

		</div>
	</div>	
</div>

{!! $inventories->appends(request()->query())->render() !!}

@stop


@section('scripts')
<script type="text/javascript">
	$( "i.fa.fa-times" ).click(function() {
		var record_removal = confirm("{{trans('validation.confirm_delete', ['attribute' => 'device inventory'])}}");

		if (record_removal == true) {
			//alert('delete_inventory_'+this.id);
			document.getElementById('delete_inventory_'+this.id).submit();
		} else {
			return false;
		}
	});
</script>
@stop