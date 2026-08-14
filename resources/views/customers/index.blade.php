@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Customer Search Panel</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('customer.index'),'class'=>'form-horizontal','role'=>'search'])  !!}
					<div class="form-group col-md-6 ">
							{!! Form::label('name', 'Name', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('name', old('name'), array('class' => 'form-control', 'placeholder' => 'Customer Name...')); !!}
						</div>
					</div>

					<div class="form-group col-md-6 ">
							{!! Form::label('email', 'Email', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
			            	{!! Form::text('email', old('email'), array('class' => 'form-control', 'placeholder' => 'Email...')); !!}
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
				<caption>Customer List</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Email</th>
						<th>Gender</th>
						<th>Date of Birth</th>
						<th>Mobile Number</th>
						<th>State</th>
						<th>Created By</th>
						<th>Action</th>
					</tr>
				</thead>
				
				<tbody>
					@foreach ($customers as $index => $customer)
					<tr>
						<td>{{ (($customers->currentPage() - 1 ) * $customers->perPage()) + $index + 1 }}</td>
						<td>{{ $customer->name }}</td>
						<td>{{ $customer->email }}</td>
						<td>{{ ucfirst($customer->gender) }}</td>
						<td>{{ $customer->dob }}</td>
						<td>{{ $customer->mobile_number }}</td>
						<td>{{ $customer->state->state_name }}</td>
						<td>{{ $customer->creator->name }}</td>
						<td>
							<a href="{{ route('customer.edit', $customer->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>&nbsp;&nbsp;
							{{--
							@can('super_admin')
							{!! Form::model($customer, array('method' => 'delete', 'route' => ['customer.destroy', $customer->id], 'id' => 'delete_customer_'.$customer->id, 'style' => 'display:inline')) !!}
							<i id="{{$customer->id}}" class="fa fa-times"></i>
			                {!! Form::close() !!}
			                @endcan
			                --}}
						</td>
					</tr>
				    @endforeach
				</tbody>
			</table>

        </div>
    </div>
</div>

{!! $customers->appends(request()->query())->render() !!}

@stop


@section('scripts')
<script type="text/javascript">
	$( "i.fa.fa-times" ).click(function() {
		var record_removal = confirm("{{trans('validation.confirm_delete', ['attribute' => 'customer'])}}");

		if (record_removal == true) {
			//alert('delete_customer_'+this.id);
			document.getElementById('delete_customer_'+this.id).submit();
		} else {
			return false;
		}
	});
</script>
@stop