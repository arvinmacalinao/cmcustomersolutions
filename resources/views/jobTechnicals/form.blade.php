@inject('states', 'App\Http\Utilities\State')
@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@section('styles')
<link rel="stylesheet" href="/css/jquery.multiselect.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
@stop

<fieldset>
    <legend>Job Details</legend>

    @if($tech_job->job->image)
    <div class="form-group">
        {!! Form::label('', 'Image', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            <img src="{{url('/images/job/'.$tech_job->job->image)}}">
        </div>
    </div>
    @endif

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Job No.</b>
        </div>
        <div class="col-md-7">
            {{ sprintf('J0%08d', $tech_job->job->id) }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Device Model</b>
        </div>
        <div class="col-md-7">
            {{ $tech_job->job->device->inventory->model->name }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Device Warranty</b>
        </div>
        <div class="col-md-7">
            {{ $globalVar::getWarrantyStatus()[$tech_job->job->device->warranty_status] }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Job Note</b>
        </div>
        <div class="col-md-7">
            {{ $tech_job->job->note }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Complaints</b>
        </div>
        <div class="col-md-7">
            @foreach($tech_job->job->complaints as $complaint)
                @if ($complaint == $tech_job->job->complaints->last())
                    {{ $complaint->name }}
                @else
                    {{ $complaint->name }}, 
                @endif
            @endforeach
        </div>
    </div> 
</fieldset>

<fieldset>
    <legend>Technician Feedback</legend>

    <div class="form-group {{ $errors->has('job_level_id') ? ' has-error' : '' }}">
        {!! Form::label('job_level_id', 'Job Level', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            @if ($tech_job->job->job_level_id == 3)
            {!! 
                Form::select('job_level_id', $job_levels, $tech_job->job->job_level_id, ['class' => 'form-control', 
                                                                                        'id' => 'job_level_id', 
                                                                                        'disabled']); 
            !!} 
            @else
            {!! 
                Form::select('job_level_id', $job_levels, $tech_job->job->job_level_id, ['class' => 'form-control', 
                                                                                        'id' => 'job_level_id']); 
            !!}
            @endif

            @if ($errors->has('job_level_id'))
            <span class="help-block">
                <strong>{{ $errors->first('job_level_id') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('technical_remark_id') ? ' has-error' : '' }}">
        {!! Form::label('technical_remark_id', 'Technical Remarks', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            <select class="js-example-basic-multiple form-control" name="technical_remark_id[]" multiple="multiple" id="technical_remark_id">
                @foreach ($remark_list as $key => $remark)
                <option value="{{$key}}" {!! in_array($key, $selected_remarks) ? "selected":"" !!}>{{ $remark }}</option>
                @endforeach
            </select>

            @if ($errors->has('technical_remark_id'))
            <span class="help-block">
                <strong>{{ $errors->first('technical_remark_id') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('remark') ? ' has-error' : '' }}">
        {!! Form::label('remark', 'Other Remarks', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::text('remark', old('remark'), array('class' => 'form-control', 'id' => 'remark')); !!}

            @if ($errors->has('remark'))
                <span class="help-block">
                <strong>{{ $errors->first('remark') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('void_warranty') ? ' has-error' : '' }}">
        {!! Form::label('void_warranty', 'Void Warranty', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            @if($tech_job->job->device->warranty_status == 3)
                {!! Form::checkbox('void_warranty', '3', true, ['id' => 'void_warranty']); !!} 
            @else
                {!! Form::checkbox('void_warranty', '3', old('void_warranty'), ['id' => 'void_warranty']); !!}
            @endif

            @if ($errors->has('void_warranty'))
            <span class="help-block">
                <strong>{{ $errors->first('void_warranty') }}</strong>
            </span>
            @endif
        </div>
    </div>

    {!! Form::hidden('job_id', $tech_job->job_id) !!}
    
</fieldset>

@section('scripts')
<script type="text/javascript" src="/js/jquery.multiselect.js"> </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

<script type="text/javascript">

    if( {{$tech_job->job->job_level_id}} != 3 &&  $("#job_level_id").val() == 3 ) {
        $('.btn-primary').prop("disabled", true); //update button
        $('.btn-success').prop("disabled", true); //complete button
    }

    if( {{$tech_job->job->job_level_id}} == 3) {
        document.getElementById("job_level_id").disabled = true;
    }

    $(".js-example-basic-multiple").select2();

    $('#job_level_id').on('change', function() {
        if( this.value == 3 && {{$tech_job->job->company_id}} !=  1 ) {
            $('.btn-primary').prop("disabled", true); //update button
            $('.btn-success').prop("disabled", true); //complete button
        } else {
            $('.btn-primary').prop("disabled", false); //update button
            $('.btn-success').prop("disabled", false); //complete button
        }
    })

    $('#update_btn').click(function() {
        if( {{$tech_job->job->job_level_id}} != 3 &&  $("#job_level_id").val() == 3 ) {
            alert('You are only allow to change the job level to 3 by completing the technical job.');
            return false;
        } 
    });

</script>
@stop