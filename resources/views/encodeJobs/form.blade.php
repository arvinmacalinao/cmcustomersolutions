@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@section('styles')
<link rel="stylesheet" href="/css/jquery.multiselect.css">
@stop

<fieldset>
    <legend>Job Details</legend>

    @if($encode_job->jobLogistic->job->image)
    <div class="form-group">
        {!! Form::label('', 'Image', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            <img src="{{url('/images/job/'.$encode_job->jobLogistic->job->image)}}">
        </div>
    </div>
    @endif

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>IMEI</b>
        </div>
        <div class="col-md-7">
            {{ $encode_job->jobLogistic->job->imei }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Device Model</b>
        </div>
        <div class="col-md-7">
            {{ $encode_job->jobLogistic->job->device->inventory->model->name }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Device Warranty</b>
        </div>
        <div class="col-md-7">
            {{ $globalVar::getWarrantyStatus()[$encode_job->jobLogistic->job->warranty] }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Job Note</b>
        </div>
        <div class="col-md-7">
            {{ $encode_job->jobLogistic->job->note }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Complaints</b>
        </div>
        <div class="col-md-7">
            @foreach($encode_job->jobLogistic->job->complaints as $complaint)
                @if ($complaint == $encode_job->jobLogistic->job->complaints->last())
                    {{ $complaint->name }}
                @else
                    {{ $complaint->name }}, 
                @endif
            @endforeach
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>Encode Description</legend>
    <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
        {!! Form::label('description', 'Description', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::text('description', old('description'), array('class' => 'form-control', 'id' => 'description', 'maxlength' => '250')); !!}

            @if ($errors->has('description'))
                <span class="help-block">
                <strong>{{ $errors->first('description') }}</strong>
            </span>
            @endif
        </div>
    </div>
</fieldset>