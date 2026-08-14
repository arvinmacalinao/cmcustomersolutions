@if( count($accessories) > 0 )
    {!! Form::label('bom_id', 'Accessories', array('class' => 'col-md-3 control-label')); !!}

    <div class="col-md-7">
    @foreach($accessories as $accessory)
        {!! Form::checkbox('bom_id[]', $accessory->id); !!} {!! $accessory->name; !!}  &nbsp;&nbsp;
    @endforeach

    @if ($errors->has('bom_id'))
    <span class="help-block">
        <strong>{{ $errors->first('bom_id') }}</strong>
    </span>
    @endif
    </div>
@else
    <p>No accessories found. You may add the model's BOM <a href={{ route('model.bom.create') }} target="_blank">here</a></p>.
@endif