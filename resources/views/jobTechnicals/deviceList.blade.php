@if( isset($device) )
    @if( isset($device['error']) )
        {{ $device['error'] }}
    @else
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
                    <td>{!! Form::radio('imei', $device->imei, true); !!}</td>
                    @else
                    <td>{!! Form::radio('imei', $device->imei, false, ['disabled' => 'disabled']); !!}</td>
                    @endif
                    <td>{{ $device->model->name }}</td>
                    <td>{{ $device->imei }}</td>
                    <td>{{ $device->registration ? $device->registration->customer->name : '-' }}</td>
                    <td>{{ $device->registration ? $device->registration->customer->email : '-' }}</td>
                    <td>{{ $device->registration ? 'Registered' : 'Not Registered' }}</td>
                </tr>
            </tbody>
        </table>

        @if ( !isset($device->registration) ) 
        <p>The device has not been registered. You may register the customer's device <a href={{ route('device_registration.create') }} target="_blank">here</a>.</p>
        @endif
    @endif

@else
    <p>IMEI not found. You may add the device's IMEI to the system inventory <a href={{ route('device_inventory.create') }} target="_blank">here</a></p>.
@endif