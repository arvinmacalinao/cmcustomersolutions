@inject('states', 'App\Http\Utilities\State')
@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
    {!! Form::label('name', 'Customer Name', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('name', old('name'), array('class' => 'form-control', 'required')); !!}

        @if ($errors->has('name'))
            <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
    {!! Form::label('email', 'Email', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::email('email', null, array('class' => 'form-control', 'maxlength' => '45')); !!}

        @if ($errors->has('email'))
        <span class="help-block">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('gender') ? ' has-error' : '' }}">
    {!! Form::label('gender', 'Gender', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::select('gender', array('male' => 'Male', 'female' => 'Female'), null, ['class' => 'form-control', 'required']); !!} 

        @if ($errors->has('gender'))
        <span class="help-block">
            <strong>{{ $errors->first('gender') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('dob') ? ' has-error' : '' }}">
    {!! Form::label('dob', 'Date of Birth', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        <div class='input-group date'>
            {!! Form::date('dob', old('dob'), ['placeholder' => 'Pick Customer Date of Birth...', 'class' => 'form-control']); !!}
            <span class="input-group-addon">
                <span class="fa fa-calendar"></span>
            </span>
        </div>

        @if ($errors->has('dob'))
        <span class="help-block">
            <strong>{{ $errors->first('dob') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('id_type') ? ' has-error' : '' }}">
    {!! Form::label('id_type', 'ID Type', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::select('id_type', $globalVar::getCustomerIDType(), null, ['placeholder' => 'Pick Customer ID...', 'class' => 'form-control', 'required']); !!}

        @if ($errors->has('id_type'))
        <span class="help-block">
            <strong>{{ $errors->first('id_type') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('id_number') ? ' has-error' : '' }}">
    {!! Form::label('id_number', 'ID Number', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('id_number', null, array('class' => 'form-control', 'maxlength' => '20', 'required')); !!}

        @if ($errors->has('id_number'))
        <span class="help-block">
            <strong>{{ $errors->first('id_number') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('mobile_number') ? ' has-error' : '' }}">
    {!! Form::label('mobile_number', 'Mobile Number', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('mobile_number', null, array('class' => 'form-control', 'maxlength' => '20')); !!}

        @if ($errors->has('mobile_number'))
        <span class="help-block">
            <strong>{{ $errors->first('mobile_number') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('home_number') ? ' has-error' : '' }}">
    {!! Form::label('home_number', 'Home Number', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('home_number', null, array('class' => 'form-control', 'maxlength' => '20')); !!}

        @if ($errors->has('home_number'))
        <span class="help-block">
            <strong>{{ $errors->first('home_number') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('fax_number') ? ' has-error' : '' }}">
    {!! Form::label('fax_number', 'Fax Number', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('fax_number', null, array('class' => 'form-control', 'maxlength' => '20')); !!}

        @if ($errors->has('fax_number'))
        <span class="help-block">
            <strong>{{ $errors->first('fax_number') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('address') ? ' has-error' : '' }}">
    {!! Form::label('address', 'Address', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('address', null, array('class' => 'form-control', 'maxlength' => '250', 'required')); !!}

        @if ($errors->has('address'))
        <span class="help-block">
            <strong>{{ $errors->first('address') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('postcode') ? ' has-error' : '' }}">
    {!! Form::label('postcode', 'Postcode', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('postcode', null, array('class' => 'form-control', 'maxlength' => '250')); !!}

        @if ($errors->has('postcode'))
        <span class="help-block">
            <strong>{{ $errors->first('postcode') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('state_id') ? ' has-error' : '' }}">
    <label class="col-md-4 control-label" for="state_id">State</label>

    <div class="col-md-6">
        {!! Form::select('state_id', $states::all(), old('state_id'), ['placeholder' => 'Pick a state...', 'class' => 'form-control', 'required']); !!} 

        @if ($errors->has('state_id'))
        <span class="help-block">
            <strong>{{ $errors->first('state_id') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('country_id') ? ' has-error' : '' }}">
    <label class="col-md-4 control-label" for="country_id">Country</label>

    <div class="col-md-6">
        {!! Form::select('country_id', ['1' => 'Philippines'], 1, ['class' => 'form-control']); !!} 

        @if ($errors->has('country_id'))
        <span class="help-block">
            <strong>{{ $errors->first('country_id') }}</strong>
        </span>
        @endif
    </div>
</div>