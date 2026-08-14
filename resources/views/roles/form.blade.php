<div class="form-group {{ $errors->has('role_label') ? ' has-error' : '' }}">
    {!! Form::label('role_label', 'Role', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('role_label', old('role_label'), array('class' => 'form-control', 'required')); !!}
        <p class="help-block">How users would view the role.</p>

        @if ($errors->has('role_label'))
            <span class="help-block">
            <strong>{{ $errors->first('role_label') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('role_name') ? ' has-error' : '' }}">
    {!! Form::label('role_name', 'Role Name', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('role_name', old('role_name'), array('class' => 'form-control', 'required')); !!}
        <p class="help-block">Eg: role_example.</p>

        @if ($errors->has('role_name'))
            <span class="help-block">
            <strong>{{ $errors->first('role_name') }}</strong>
        </span>
        @endif
    </div>
</div>