@inject('companies', 'App\Http\Utilities\Company')
@inject('roles', 'App\Http\Utilities\Role')

<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
    {!! Form::label('name', 'Name', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('name', old('name'), array('class' => 'form-control', 'required')); !!}

        @if ($errors->has('name'))
            <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
    <label class="col-md-4 control-label">E-Mail Address</label>

    <div class="col-md-6">
        {!! Form::text('email', old('email'), $attributes = ['class' => 'form-control', 'required' => 'required']); !!}

        @if ($errors->has('email'))
            <span class="help-block">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('role_id') ? ' has-error' : '' }}">
    <label class="col-md-4 control-label">User Role</label>

    <div class="col-md-6">
        {!! Form::select('role_id', $roles::all(), old('role_id'), ['placeholder' => 'Pick a role...', 'class' => 'form-control']); !!} 

        @if ($errors->has('role_id'))
        <span class="help-block">
            <strong>{{ $errors->first('role_id') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('company_id') ? ' has-error' : '' }}">
    <label class="col-md-4 control-label">Company</label>

    <div class="col-md-6">
        {!! Form::select('company_id', $companies::all(), old('company_id'), ['placeholder' => 'Pick a company...', 'class' => 'form-control']); !!} 

        @if ($errors->has('company_id'))
        <span class="help-block">
            <strong>{{ $errors->first('company_id') }}</strong>
        </span>
        @endif
    </div>
</div>