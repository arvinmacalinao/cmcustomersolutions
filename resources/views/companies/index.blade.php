@extends('layout')

@section('content')

<div class="content">

<table class="table">
	<caption>Company List</caption>
	<thead>
		<tr>
			<th>#</th>
			<th>Company</th>
			<th>Type</th>
			<th>Prefix</th>
			<th>Email</th>
			<th>Contact</th>
			<th>State</th>
			<th>Created By</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($companies as $index => $company)
		<tr>
			<td>{{ (($companies->currentPage() - 1 ) * $companies->perPage()) + $index + 1 }}</td>
			<td>{{ $company->company_name }}</td>
			<td>{{ $company->company_type }}</td>
			<td>{{ $company->company_prefix }}</td>
			<td>{{ $company->email }}</td>
			<td>{{ $company->contact_number }}</td>
			<td>{{ $company->state->state_name }}</td>
			<td>{{ $company->creator->name }}</td>
			<td>
				<a href="{{ route('company.edit', $company->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>&nbsp;&nbsp;
				@can('super_admin')
				{!! Form::model($company, array('method' => 'delete', 
												'route' => ['company.destroy', $company->id], 
												'id' => 'delete_company_'.$company->id, 
												'style' => 'display:inline')) !!}
				<i id="{{$company->id}}" class="fa fa-times"></i>
                {!! Form::close() !!}
                @endcan
			</td>
		</tr>
	    @endforeach
	</tbody>
</table>
	
</div>

{!! $companies->appends(request()->query())->render() !!}

@stop


@section('scripts')
<script type="text/javascript">
	$( "i.fa.fa-times" ).click(function() {
		var record_removal = confirm("{{trans('validation.confirm_delete', ['attribute' => 'company'])}}");

		if (record_removal == true) {
			//alert('delete_inventory_'+this.id);
			document.getElementById('delete_company_'+this.id).submit();
		} else {
			return false;
		}
	});
</script>
@stop