@extends('layout')

@section('content')

<div class="content">

<table class="table">
	<caption>Permission List</caption>
	<thead>
		<tr>
			<th>#</th>
			<th>Permission Label</th>
			<th>Permission Name</th>
			<th>Description</th>
			<th>Created By</th>
			<th>Created At</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($permissions as $index => $permission)
		<tr>
			<td>{{ (($permissions->currentPage() - 1 ) * $permissions->perPage()) + $index + 1 }}</td>
			<td>{{ $permission->permission_label }}</td>
			<td>{{ $permission->permission_name }}</td>
			<td>{{ $permission->description }}</td>
			<td>{{ $permission->creator->name }}</td>
			<td>{{ $permission->created_at }}</td>
			<td>
				<a href="{{ route('permission.edit', $permission->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>&nbsp;&nbsp;
				{!! Form::model($permission, array('method' => 'delete', 'route' => ['permission.destroy', $permission->id], 'id' => 'delete_permission_'.$permission->id, 'style' => 'display:inline')) !!}
				<i id="{{$permission->id}}" class="fa fa-times"></i>
                {!! Form::close() !!}
			</td>
		</tr>
	        
	    @endforeach
		
	</tbody>
</table>
	
</div>

{!! $permissions->appends(request()->query())->render() !!}

@stop


@section('scripts')
<script type="text/javascript">
	$( "i.fa.fa-times" ).click(function() {
		var record_removal = confirm("{{trans('validation.confirm_delete', ['attribute' => 'permission'])}}");

		if (record_removal == true) {
			//alert('delete_inventory_'+this.id);
			document.getElementById('delete_permission_'+this.id).submit();
		} else {
			return false;
		}
	});
</script>
@stop