@extends('layout')

@section('content')

<div class="content">

<table class="table">
	<caption>Brand List</caption>
	<thead>
		<tr>
			<th>#</th>
			<th>Brand</th>
			<th>Created By</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($brands as $index => $brand)
		<tr>
			<td>{{ (($brands->currentPage() - 1 ) * $brands->perPage()) + $index + 1 }}</td>
			<td>{{ $brand->name }}</td>
			<td>{{ $brand->creator->name }}</td>
			<td>
				<a href="{{ route('brand.edit', $brand->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>&nbsp;&nbsp;
				@can('brand_mgmt')
				{!! Form::model($brand, array('method' => 'delete', 'route' => ['brand.destroy', $brand->id], 'id' => 'delete_brand_'.$brand->id, 'style' => 'display:inline')) !!}
				<i id="{{$brand->id}}" class="fa fa-times"></i>
                {!! Form::close() !!}
                @endcan
			</td>
		</tr>
	    @endforeach
	</tbody>
</table>
	
</div>

{!! $brands->appends(request()->query())->render() !!}

@stop


@section('scripts')
<script type="text/javascript">
	$( "i.fa.fa-times" ).click(function() {
		var record_removal = confirm("{{trans('validation.confirm_delete', ['attribute' => 'brand'])}}");

		if (record_removal == true) {
			//alert('delete_inventory_'+this.id);
			document.getElementById('delete_brand_'+this.id).submit();
		} else {
			return false;
		}
	});
</script>
@stop