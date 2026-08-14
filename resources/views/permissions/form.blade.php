<div class="form-group {{ $errors->has('permission_label') ? ' has-error' : '' }}">
    {!! Form::label('permission_label', 'Permission', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('permission_label', old('permission_label'), array('class' => 'form-control', 'required')); !!}
        <p class="help-block">How users would view the permission.</p>

        @if ($errors->has('permission_label'))
            <span class="help-block">
            <strong>{{ $errors->first('permission_label') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('permission_name') ? ' has-error' : '' }}">
    {!! Form::label('permission_name', 'Permission Name', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('permission_name', old('permission_name'), array('class' => 'form-control', 'required')); !!}
        <p class="help-block">The value system would use for validation. Eg: permission_example.</p>

        @if ($errors->has('permission_name'))
            <span class="help-block">
            <strong>{{ $errors->first('permission_name') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
    {!! Form::label('description', 'Description', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('description', old('description'), array('class' => 'form-control')); !!}
        <p class="help-block">Description of the permission.</p>

        @if ($errors->has('description'))
            <span class="help-block">
            <strong>{{ $errors->first('description') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('parent_id') ? ' has-error' : '' }}">
    {!! Form::label('parent_id', 'Permission Parent', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::select('parent_id', $permission_list, old('parent_id'), ['placeholder' => 'Pick a permission parent...', 'class' => 'form-control']); !!} 

        @if ($errors->has('parent_id'))
            <span class="help-block">
            <strong>{{ $errors->first('parent_id') }}</strong>
        </span>
        @endif
    </div>
</div>