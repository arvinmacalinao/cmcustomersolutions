@extends('layout')

@section('content')

<div class="content">

<table class="table">
	<caption>Role List</caption>
	<thead>
		<tr>
			<th>#</th>
			<th>Role Label</th>
			<th>Role Name</th>
			<th>Created By</th>
			<th>Created At</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($roles as $index => $role)
		<tr>
			<td>{{ (($roles->currentPage() - 1 ) * $roles->perPage()) + $index + 1 }}</td>
			<td>{{ $role->role_label }}</td>
			<td>{{ $role->role_name }}</td>
			<td>{{ $role->creator->name }}</td>
			<td>{{ $role->created_at }}</td>
			<td>
				<a href="{{ route('role.edit', $role->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>&nbsp;&nbsp;
				@can('super_admin')
				{!! Form::model($role, array('method' => 'delete', 'route' => ['role.destroy', $role->id], 'id' => 'delete_role_'.$role->id, 'style' => 'display:inline')) !!}
				<i id="{{$role->id}}" class="fa fa-times"></i>
                {!! Form::close() !!}
                @endcan
			</td>
		</tr>
	    @endforeach
	</tbody>
</table>
	
</div>

{!! $roles->appends(request()->query())->render() !!}

@stop


@section('scripts')
<script type="text/javascript">
	$( "i.fa.fa-times" ).click(function() {
		var record_removal = confirm("{{trans('validation.confirm_delete', ['attribute' => 'role'])}}");

		if (record_removal == true) {
			//alert('delete_inventory_'+this.id);
			document.getElementById('delete_role_'+this.id).submit();
		} else {
			return false;
		}
	});
</script>
@stop