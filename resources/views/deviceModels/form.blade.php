@inject('brands', 'App\Http\Utilities\Brand')

<div class="form-group {{ $errors->has('code') ? ' has-error' : '' }}">
    {!! Form::label('code', 'Model Code', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('code', old('code'), array('class' => 'form-control', 'required')); !!}

        @if ($errors->has('code'))
            <span class="help-block">
            <strong>{{ $errors->first('code') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
    {!! Form::label('name', 'Model Name', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('name', old('name'), array('class' => 'form-control', 'required')); !!}

        @if ($errors->has('name'))
            <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('brand_id') ? ' has-error' : '' }}">
    {!! Form::label('brand_id', 'Brand', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::select('brand_id', $brands::all(), old('brand_id'), ['placeholder' => 'Pick a brand...', 'class' => 'form-control', 'required']); !!} 

        @if ($errors->has('brand_id'))
        <span class="help-block">
            <strong>{{ $errors->first('brand_id') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('device_type_id') ? ' has-error' : '' }}">
    {!! Form::label('device_type_id', 'Device Type', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::select('device_type_id', $device_types, old('device_type_id'), ['placeholder' => 'Pick the device type...', 'class' => 'form-control', 'required']); !!} 

        @if ($errors->has('device_type_id'))
        <span class="help-block">
            <strong>{{ $errors->first('device_type_id') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('warranty') ? ' has-error' : '' }}">
    {!! Form::label('warranty', 'Warranty in Months', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::number('warranty', null, array('class' => 'form-control', 'min' => '0', 'max' => '200', 'required')); !!}

        @if ($errors->has('warranty'))
        <span class="help-block">
            <strong>{{ $errors->first('warranty') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('price') ? ' has-error' : '' }}">
    {!! Form::label('price', 'Price', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::number('price', null, array('class' => 'form-control', 'min' => '0', 'max' => '999999.99', 'step' => '0.01', 'required')); !!}

        @if ($errors->has('price'))
        <span class="help-block">
            <strong>{{ $errors->first('price') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('labor_cost_1') ? ' has-error' : '' }}">
    {!! Form::label('labor_cost_1', 'Labor Cost 1', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::number('labor_cost_1', null, array('class' => 'form-control', 'min' => '0', 'max' => '999999.99', 'step' => '0.01', 'required')); !!}

        @if ($errors->has('labor_cost_1'))
        <span class="help-block">
            <strong>{{ $errors->first('labor_cost_1') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('labor_cost_2') ? ' has-error' : '' }}">
    {!! Form::label('labor_cost_2', 'Labor Cost 2', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::number('labor_cost_2', null, array('class' => 'form-control', 'min' => '0', 'max' => '999999.99', 'step' => '0.01', 'required')); !!}

        @if ($errors->has('labor_cost_2'))
        <span class="help-block">
            <strong>{{ $errors->first('labor_cost_2') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('labor_cost_3') ? ' has-error' : '' }}">
    {!! Form::label('labor_cost_3', 'Labor Cost 3', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::number('labor_cost_3', null, array('class' => 'form-control', 'min' => '0', 'max' => '999999.99', 'step' => '0.01', 'required')); !!}

        @if ($errors->has('labor_cost_3'))
        <span class="help-block">
            <strong>{{ $errors->first('labor_cost_3') }}</strong>
        </span>
        @endif
    </div>
</div>