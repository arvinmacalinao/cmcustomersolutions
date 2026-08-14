<div class="form-group {{ $errors->has('code') ? ' has-error' : '' }}">
    {!! Form::label('code', 'BOM Item Code', array('class' => 'col-md-3 control-label')); !!}

    <div class="col-md-7">
        {!! Form::text('code', old('code'), ['placeholder' => 'BOM Item Code...', 'class' => 'form-control', 'required']); !!}

        @if ($errors->has('code'))
            <span class="help-block">
            <strong>{{ $errors->first('code') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
    {!! Form::label('name', 'BOM Item Name', array('class' => 'col-md-3 control-label')); !!}

    <div class="col-md-7">
        {!! Form::text('name', old('name'), ['placeholder' => 'BOM Item Name...', 'class' => 'form-control', 'required']); !!}

        @if ($errors->has('name'))
            <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('brand_id') ? ' has-error' : '' }}">
    {!! Form::label('brand_id', 'Brand', array('class' => 'col-md-3 control-label')); !!}

    <div class="col-md-7">
        {!! Form::select('brand_id', $brands, old('brand_id'), ['class' => 'form-control', 'required']); !!} 

        @if ($errors->has('brand_id'))
        <span class="help-block">
            <strong>{{ $errors->first('brand_id') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('warranty') ? ' has-error' : '' }}">
    {!! Form::label('warranty', 'Warranty (in months)', array('class' => 'col-md-3 control-label')); !!}

    <div class="col-md-7">
        {!! Form::number('warranty', old('warranty'), ['placeholder' => 'BOM Item Warranty...', 'class' => 'form-control', 'min' => '0', 'max' => '50']); !!}

        @if ($errors->has('warranty'))
            <span class="help-block">
            <strong>{{ $errors->first('warranty') }}</strong>
        </span>
        @endif
    </div>
</div>

@if(Gate::check('super_admin'))
<div class="form-group {{ $errors->has('quantity') ? ' has-error' : '' }}">
    {!! Form::label('quantity', 'Quantity', array('class' => 'col-md-3 control-label')); !!}

    <div class="col-md-7">
        {!! Form::number('quantity', old('quantity'), ['placeholder' => 'Quantity...', 'class' => 'form-control', 'min' => '0']); !!}

        @if ($errors->has('quantity'))
            <span class="help-block">
            <strong>{{ $errors->first('quantity') }}</strong>
        </span>
        @endif
    </div>
</div>
@endif

<div class="form-group {{ $errors->has('suggested_retail_price') ? ' has-error' : '' }}">
    {!! Form::label('suggested_retail_price', 'SRP', array('class' => 'col-md-3 control-label')); !!}

    <div class="col-md-7">
        {!! Form::number('suggested_retail_price', old('suggested_retail_price'), array('class' => 'form-control', 'min' => '0', 'max' => '999999.99', 'step' => '0.01')); !!}

        @if ($errors->has('suggested_retail_price'))
        <span class="help-block">
            <strong>{{ $errors->first('suggested_retail_price') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('retail_price') ? ' has-error' : '' }}">
    {!! Form::label('retail_price', 'Retail Price', array('class' => 'col-md-3 control-label')); !!}

    <div class="col-md-7">
        {!! Form::number('retail_price', old('retail_price'), array('class' => 'form-control', 'min' => '0', 'max' => '999999.99', 'step' => '0.01')); !!}

        @if ($errors->has('retail_price'))
        <span class="help-block">
            <strong>{{ $errors->first('retail_price') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('dealer_price') ? ' has-error' : '' }}">
    {!! Form::label('dealer_price', 'Dealer Price', array('class' => 'col-md-3 control-label')); !!}

    <div class="col-md-7">
        {!! Form::number('dealer_price', old('dealer_price'), array('class' => 'form-control', 'min' => '0', 'max' => '999999.99', 'step' => '0.01')); !!}

        @if ($errors->has('dealer_price'))
        <span class="help-block">
            <strong>{{ $errors->first('dealer_price') }}</strong>
        </span>
        @endif
    </div>
</div>