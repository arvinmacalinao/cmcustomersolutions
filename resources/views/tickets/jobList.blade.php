@inject('param', 'App\Http\Utilities\GlobalConstant')

@if( isset($job) )
    <table class="table">
        <thead>
            <tr>
                <th>Job ID</th>
                <th>Model</th>
                <th>IMEI</th>
                <th>Contact Name</th>
                <th>Mobile No.</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody id="device-result">
            <tr>
                <td>{{ sprintf('JO%08d', $job->id) }}</td>
                <td>{{ $job->device->inventory->model->name }}</td>
                <td>{{ $job->imei }}</td>
                <td>{{ $job->contact_name }}</td>
                <td>{{ $job->mobile_number }}</td>
                <td>{{ $job->status->name }}</td>
                <td>{{ $job->created_at }}</td>
            </tr>
        </tbody>
    </table>

    <input type="hidden" name="job_id" value={{$job->id}}>
@else
    <p>No job has been created for IMEI: {{ $job->imei }}</p>.
@endif