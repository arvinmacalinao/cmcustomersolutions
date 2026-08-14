<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
    {!! Form::label('name', 'Complaint', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('name', old('name'), array('class' => 'form-control', 'required')); !!}

        @if ($errors->has('name'))
            <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group {{ $errors->has('code') ? ' has-error' : '' }}">
    {!! Form::label('code', 'Complaint Code', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        {!! Form::text('code', old('code'), array('class' => 'form-control', 'required')); !!}

        @if ($errors->has('code'))
        <span class="help-block">
            <strong>{{ $errors->first('code') }}</strong>
        </span>
        @endif
    </div>
</div>

@if( !isset($complaint) || $complaint->parent_id != 0 )
<div class="form-group {{ $errors->has('parent_id') ? ' has-error' : '' }}">
    {!! Form::label('parent_id', 'Complaint Category', array('class' => 'col-md-4 control-label')); !!}

    <div class="col-md-6">
        @if( isset($complaint) && $complaint->parent_id != 0 )
        {!! Form::select('parent_id', $complaint_category, old('parent_id'), ['placeholder' => 'Pick a category...', 'class' => 'form-control', 'required']); !!} 
        @else
        {!! Form::select('parent_id', $complaint_category, old('parent_id'), ['placeholder' => 'Pick a category...', 'class' => 'form-control']); !!} 
        @endif

        @if ($errors->has('parent_id'))
        <span class="help-block">
            <strong>{{ $errors->first('parent_id') }}</strong>
        </span>
        @endif
    </div>
</div>
@endif