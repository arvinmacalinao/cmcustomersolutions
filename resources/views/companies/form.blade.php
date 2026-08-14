@inject('states', 'App\Http\Utilities\State')

<div class="form-group {{ $errors->has('company_name') ? ' has-error' : '' }}">
    {!! Form::label('company_name', 'Company Name', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('company_name', old('company_name'), array('class' => 'form-control', 'required')); !!}

        @if ($errors->has('company_name'))
            <span class="help-block">
            <strong>{{ $errors->first('company_name') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('company_type') ? ' has-error' : '' }}">
    {!! Form::label('company_type', 'Company Type', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::select('company_type', array('branch' => 'Branch', 'dealer' => 'Dealer'), null, ['placeholder' => 'Pick a company type...', 'class' => 'form-control', 'required']); !!} 

        @if ($errors->has('company_type'))
        <span class="help-block">
            <strong>{{ $errors->first('company_type') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('company_prefix') ? ' has-error' : '' }}">
    {!! Form::label('company_prefix', 'Company Prefix', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('company_prefix', null, array('class' => 'form-control', 'maxlength' => '2')); !!}

        @if ($errors->has('company_prefix'))
        <span class="help-block">
            <strong>{{ $errors->first('company_prefix') }}</strong>
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

<div class="form-group {{ $errors->has('contact_number') ? ' has-error' : '' }}">
    {!! Form::label('contact_number', 'Contact Number', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('contact_number', null, array('class' => 'form-control', 'maxlength' => '20', 'required')); !!}

        @if ($errors->has('contact_number'))
        <span class="help-block">
            <strong>{{ $errors->first('contact_number') }}</strong>
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