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
                </tr>
            </thead>
            <tbody id="device-result">
                <tr>
                    <td>{!! Form::radio('imei', $device->imei, true, ['class' => 'imei', 'id' => 'imei']); !!}</td>
                    <td>{{ $device->model->name }}</td>
                    <td>{{ $device->imei }}</td>
                </tr>
            </tbody>
        </table>
    @endif
@else
    <p>IMEI not found.</p>.
@endif



