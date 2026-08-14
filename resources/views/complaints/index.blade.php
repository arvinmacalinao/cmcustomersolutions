@extends('layout')

@section('content')

<div class="content">

<table class="table">
	<caption>Complaint List</caption>
	<thead>
		<tr>
			<th>#</th>
			<th>Complaint</th>
			<th>Complaint Code</th>
			<th>Complaint Category</th>
			<th>Status</th>
			<th>Created By</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($complaints as $index => $complaint)
		<tr>
			<td>{{ (($complaints->currentPage() - 1 ) * $complaints->perPage()) + $index + 1 }}</td>
			<td>{{ $complaint->name }}</td>
			<td>{{ $complaint->code }}</td>
			<td>
				@if ($complaint->parent_id == 0) 
					-
				@else
					{{ $complaint_category[$complaint->parent_id] }}
				@endif
			</td>
			<td>{{ $complaint->flag ? 'Activate' : 'Deactivate' }}</td>
			<td>{{ $complaint->creator->name }}</td>
			<td>
				<a href="{{ route('complaint.edit', $complaint->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
				&nbsp;&nbsp;
				{!! Form::model($complaint, array('method' => 'delete', 
													'route' => ['complaint.destroy', $complaint->id], 
													'id' => 'delete_complaint_'.$complaint->id, 
													'style' => 'display:inline')) !!}
				<i id="{{$complaint->id}}" class="fa fa-times"></i>
                {!! Form::close() !!}
			</td>
		</tr>
	    @endforeach
	</tbody>
</table>
	
</div>

{!! $complaints->appends(request()->query())->render() !!}

@stop

@section('scripts')
<script type="text/javascript">
	$( "i.fa.fa-times" ).click(function() {
		var record_removal = confirm("{{trans('validation.confirm_delete', ['attribute' => 'complaint'])}}");

		if (record_removal == true) {
			//alert('delete_inventory_'+this.id);
			document.getElementById('delete_complaint_' + this.id).submit();
		} else {
			return false;
		}
	});
</script>
@stop