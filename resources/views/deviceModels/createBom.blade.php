@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Assign BOM Item to Model</div>
                    <div class="panel-body">
                        {!! Form::open(array( 'route' => array('model.bom.store'), 'method' => 'post', 'class' => 'form-horizontal' )) !!}
                        
                        <div class="form-group {{ $errors->has('device_model_id') ? ' has-error' : '' }}">
                            {!! Form::label('device_model_id', 'Model', array('class' => 'col-md-3 control-label')); !!}

                            <div class="col-md-7">
                                {!! Form::select('device_model_id', $device_models, old('device_model_id'), ['placeholder' => 'Select a Model...', 'class' => 'form-control', 'required']); !!} 

                                @if ($errors->has('device_model_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('device_model_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('bom_id') ? ' has-error' : '' }}">
                            {!! Form::label('bom_id', 'BOM List', array('class' => 'col-md-3 control-label')); !!}

                            <div class="col-md-7">
                                {!! Form::select('bom_id', $bom, old('bom_id'), ['placeholder' => 'Select a BOM Item...', 'class' => 'form-control', 'required']); !!} 

                                @if ($errors->has('bom_id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('bom_id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('category') ? ' has-error' : '' }}">
                            {!! Form::label('category', 'Item Category', array('class' => 'col-md-3 control-label')); !!}

                            <div class="col-md-7">
                                {!! Form::select('category', $globalVar::getBomCategory(), old('category'), ['class' => 'form-control', 'required']); !!} 

                                @if ($errors->has('category'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('category') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-md-7 col-md-offset-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-plus" aria-hidden="true"></i> Create
                                </button>
                                <a class="btn btn-danger" href="{{ route('model.bom.create') }}"><i class="fa fa-btn fa-times"></i> Cancel</a>
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>

                <div id="bom-list"></div> 
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">

    $("#device_model_id").change(function() {
        var bom_list = $("#bom-list");
        var model_id = $('#device_model_id').val();  

        //alert( "Handler for " + model_id + " called." );

        $.ajax({  
            url:"/model/bom",
            method:"get",  
            data:{ model_id : model_id},  
            dataType:"text",  
            success:function(data)  
            {  
                bom_list.html(data);
            },
            error: function(data)
            {
                var errors = data.responseJSON;
                console.log(errors);
            }  
        });
    });

    </script>
@stop