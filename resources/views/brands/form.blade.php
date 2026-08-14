<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
    {!! Form::label('name', 'Brand Name', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('name', old('name'), array('class' => 'form-control', 'required')); !!}

        @if ($errors->has('name'))
            <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>
</div>