@inject('param', 'App\Http\Utilities\GlobalConstant')

@if( isset($jobs) AND !$jobs->isEmpty())
<table class="table">
    <thead>
        <tr>
            <th style="width:30px">
                {!! Form::checkbox('checkAll', null, null, array('class' => 'form-control', 'id' => 'checkAll')); !!}
            </th>
            <th>Job No.</th>
            <th>IMEI</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Contact Name</th>
            <th>Contact No.</th>
        </tr>
    </thead>
    <tbody>
    	@foreach($jobs as $index => $job)
        <tr>
            <td style="width:30px">{!! Form::checkbox('jobs[]', $job->id); !!}</td>
            <td>{{ sprintf('JO%08d', $job->id) }}</td>
            <td>{{ $job->imei }}</td>
            <td>{{ $job->device->inventory->model->brand->name }}</td>
            <td>{{ $job->device->inventory->model->code }}</td>
            <td>{{ $job->contact_name }}</td>
            <td>{{ $job->contact_number }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>There's no device available for shipment.</p>
@endif

@section('scripts')
<script type="text/javascript">

    $('#checkAll').click(function () {    
        $('input:checkbox').prop('checked', this.checked);    
    });

</script>
@stop