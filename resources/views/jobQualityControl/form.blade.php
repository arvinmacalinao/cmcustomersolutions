@inject('states', 'App\Http\Utilities\State')
@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@section('styles')
<link rel="stylesheet" href="/css/jquery.multiselect.css">
@stop

<fieldset>
    <legend>Job Details</legend>

    @if($job_tech->job->image)
    <div class="form-group">
        {!! Form::label('', 'Image', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            <img src="{{url('/images/job/'.$job_tech->job->image)}}">
        </div>
    </div>
    @endif

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Device Model</b>
        </div>
        <div class="col-md-7">
            {{ $job_tech->job->device->inventory->model->name }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Job Level</b>
        </div>
        <div class="col-md-7">
            {{ $job_tech->job->level->name }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Job Note</b>
        </div>
        <div class="col-md-7">
            {{ $job_tech->job->note }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Complaints</b>
        </div>
        <div class="col-md-7">
            @foreach($job_tech->job->complaints as $complaint)
                @if ($complaint == $job_tech->job->complaints->last())
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

    {{--<div class="form-group">
        <div class="col-md-3" align="right">
            <b>Complaints</b>
        </div>
        <div class="col-md-7">
            @foreach($job_tech->repairs as $repair)
                @if ($repair == $job_tech->repairs->last())
                    {{ $repair->name }}
                @else
                    {{ $repair->name }}, 
                @endif
            @endforeach
        </div>
    </div> 

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Parts</b>
        </div>
        <div class="col-md-7">
            @foreach($job_tech->parts as $part)
                @if ($part == $job_tech->parts->last())
                    {{ $part->name }}
                @else
                    {{ $part->name }}, 
                @endif
            @endforeach
        </div>
    </div>--}}

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Remarks</b>
        </div>
        <div class="col-md-7">
            @foreach($job_tech->remarks as $remark)
                @if ($remark == $job_tech->remarks->last())
                    {{ $remark->name }}
                @else
                    {{ $remark->name }}, 
                @endif
            @endforeach
        </div>
    </div> 

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Other Remarks</b>
        </div>
        <div class="col-md-7">
            {{ $job_tech->remark }}
        </div>
    </div> 
    
</fieldset>

<fieldset>
    <legend>QC Feedback</legend>

    <div class="form-group {{ $errors->has('remark') ? ' has-error' : '' }}">
        {!! Form::label('remark', 'Remark', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::text('remark', '', array('class' => 'form-control', 'maxlength' => '240')); !!}

            @if ($errors->has('remark'))
                <span class="help-block">
                <strong>{{ $errors->first('remark') }}</strong>
            </span>
            @endif
        </div>
    </div>
</fieldset>