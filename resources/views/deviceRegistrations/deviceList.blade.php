@if( isset($device) )
<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Model</th>
            <th>IMEI</th>
            <th>Owner's Name</th>
            <th>Owner's Email</th>
            <th>Registered</th>
        </tr>
    </thead>
    <tbody id="device-result">
        <tr>
            @if ($device->registration) 
            <td>{!! Form::radio('imei', $device->imei, false, ['disabled' => 'disabled']); !!}</td>
            @else
            <td>{!! Form::radio('imei', $device->imei, true); !!}</td>
            @endif
            <td>{{ $device->model->code }}</td>
            <td>{{ $device->imei }}</td>
            <td>{{ $device->registration ? $device->registration->customer->name : '-' }}</td>
            <td>{{ $device->registration ? $device->registration->customer->email : '-' }}</td>
            <td>{{ $device->registration ? 'Registered' : 'Not Registered' }}</td>
        </tr>
    </tbody>
</table>
@else
<p>IMEI not found. You may register the device's IMEI <a href={{ route('device_inventory.create') }} target="_blank">here</a></p>
@endif