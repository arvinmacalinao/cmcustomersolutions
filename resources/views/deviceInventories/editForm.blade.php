<div class="form-group {{ $errors->has('imei') ? ' has-error' : '' }}">
    {!! Form::label('imei', 'IMEI', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('imei', old('imei'), array('class' => 'form-control', 'disabled')); !!}
        {!! Form::hidden('imei', old('imei')); !!}

        @if ($errors->has('imei'))
            <span class="help-block">
            <strong>{{ $errors->first('imei') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('device_model_id') ? ' has-error' : '' }}">
    {!! Form::label('device_model_id', 'Model', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::select('device_model_id', $models, old('device_model_id'), ['placeholder' => 'Pick a model...', 'class' => 'form-control', 'required']); !!} 

        @if ($errors->has('device_model_id'))
        <span class="help-block">
            <strong>{{ $errors->first('device_model_id') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('color') ? ' has-error' : '' }}">
    {!! Form::label('color', 'Model Color', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('color', old('color'), array('class' => 'form-control')); !!}

        @if ($errors->has('color'))
            <span class="help-block">
            <strong>{{ $errors->first('color') }}</strong>
        </span>
        @endif
    </div>
</div>