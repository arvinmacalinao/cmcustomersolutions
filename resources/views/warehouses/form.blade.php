<fieldset id="personalinfo">
    <legend>Warehouse Info</legend>
    <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
        {!! Form::label('name', 'Warehouse Name', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::text('name', old('name'), array('class' => 'form-control', 'required')); !!}

            @if ($errors->has('name'))
                <span class="help-block">
                <strong>{{ $errors->first('name') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('company_id') ? ' has-error' : '' }}">
        {!! Form::label('company_id', 'Company', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::select('company_id', $company_list, old('company_id'), ['class' => 'form-control', 'placeholder' => 'Select a Company...', 'required']); !!}

            @if ($errors->has('company_id'))
                <span class="help-block">
                <strong>{{ $errors->first('company_id') }}</strong>
            </span>
            @endif
        </div>
    </div>
</fieldset>

<fieldset id="personalinfo">
    <legend>Address</legend>
    <!-- Contact Name & Number Field -->
    <div class="form-group {{ $errors->has('address') ? ' has-error' : '' }}">
        {!! Form::label('address', 'Address', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::text('address', old('address'), array('class' => 'form-control', 'required')); !!}

            @if ($errors->has('address'))
                <span class="help-block">
                <strong>{{ $errors->first('address') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('postcode') ? ' has-error' : '' }}">
        {!! Form::label('postcode', 'Postcode', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::text('postcode', old('postcode'), array('class' => 'form-control', 'required')); !!}

            @if ($errors->has('postcode'))
                <span class="help-block">
                <strong>{{ $errors->first('postcode') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('state_id') ? ' has-error' : '' }}">
        {!! Form::label('state_id', 'State', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::select('state_id', $state_list, old('state_id'), ['class' => 'form-control', 'placeholder' => 'Select a State...', 'required']); !!}

            @if ($errors->has('state_id'))
                <span class="help-block">
                <strong>{{ $errors->first('state_id') }}</strong>
            </span>
            @endif
        </div>
    </div>
</fieldset>
