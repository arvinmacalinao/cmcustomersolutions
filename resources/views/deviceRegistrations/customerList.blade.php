@inject('param', 'App\Http\Utilities\GlobalConstant')

@if( isset($customers) AND !$customers->isEmpty())
<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Mobile Number</th>
            <th>ID Type</th>
            <th>ID Number</th>
        </tr>
    </thead>
    <tbody>
    	@foreach($customers as $index => $customer)
        <tr>
        	@if($index == 0)
            <td>{!! Form::radio('customer_id', $customer->id, true); !!}</td>
            @else
            <td>{!! Form::radio('customer_id', $customer->id); !!}</td>
            @endif
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->email }}</td>
            <td>{{ $customer->gender }}</td>
            <td>{{ $customer->mobile_number }}</td>
            <td>{{ $param::getCustomerIDType()[$customer->id_type] }}</td>
            <td>{{ $customer->id_number }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>Customer not found. You may register the customer's details <a href={{ route('customer.create') }} target="blank">here</a></p>
@endif