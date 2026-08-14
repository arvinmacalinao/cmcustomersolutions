@inject('param', 'App\Http\Utilities\GlobalConstant')

@if( isset($jobs) AND !$jobs->isEmpty())
<table class="table">
    <thead>
        <tr>
            <th style="width:30px">
                {!! Form::checkbox('checkAll', null, null, array('class' => 'form-control', 'id' => 'checkAll')); !!}
            </th>
            <th>Route From</th>
            <th>Job No.</th>
            <th>IMEI</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Contact Name</th>
            <th>Contact No.</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    	@foreach($jobs as $index => $jobLogistic)
        <tr>
            <td style="width:30px">{!! Form::checkbox('jobs[]', $jobLogistic->job->id); !!}</td>
            <td>{{ $jobLogistic->logistic->routeFrom->company_name }}</td>
            <td>{{ sprintf('JO%08d', $jobLogistic->job->id) }}</td>
            <td>{{ $jobLogistic->job->imei }}</td>
            <td>{{ $jobLogistic->job->device->inventory->model->brand->name }}</td>
            <td>{{ $jobLogistic->job->device->inventory->model->code }}</td>
            <td>{{ $jobLogistic->job->contact_name }}</td>
            <td>{{ $jobLogistic->job->contact_number }}</td>
            <td>
                @if($jobLogistic->job->special_case)
                &nbsp;
                @else
                <a href="{{ route('logistic.qc', array('id' => $jobLogistic->job->id)) }}"><i class="fa fa-times" aria-hidden="true"></i></a>
                @endif
            </td>
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