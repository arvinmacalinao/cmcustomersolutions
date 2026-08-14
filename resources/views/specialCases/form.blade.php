@inject('states', 'App\Http\Utilities\State')
@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@section('styles')
<link rel="stylesheet" href="/css/jquery.multiselect.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
@stop

<fieldset>
    <legend>Special Case Details</legend>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>Device Model</b>
        </div>
        <div class="col-md-7">
            {{ $case->serviceDevice->model->name }}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-3" align="right">
            <b>IMEI</b>
        </div>
        <div class="col-md-7">
            {{ $case->old_imei }}
            {!! Form::hidden('old_imei', $case->old_imei); !!}
        </div>
    </div>

</fieldset>

<fieldset>
    <legend>Special Case Feedback</legend>

    <!-- Search Device Registration, than inventories if doesn't exist; prompt link to register -->
    <div class="form-group {{ $errors->has('imei') ? ' has-error' : '' }}">
        {!! Form::label('search_imei', 'Replacement IMEI', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            <div class="input-group">
                {!! Form::text('search_imei', old('search_imei'), ['placeholder' => 'Search IMEI...', 'class' => 'form-control']); !!}
                <span class="input-group-btn">
                    <button id="device-search-button" class="btn btn-default" type="button">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>
                </span>
            </div>

            @if ($errors->has('imei'))
                <span class="help-block">
                <strong>{{ $errors->first('imei') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div id="device-result-table"></div> 

    <div class="form-group {{ $errors->has('comment') ? ' has-error' : '' }}">
        {!! Form::label('comment', 'Comment', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::text('comment', old('comment'), array('class' => 'form-control', 'id' => 'comment', 'maxlength' => 140)); !!}

            @if ($errors->has('comment'))
                <span class="help-block">
                <strong>{{ $errors->first('comment') }}</strong>
            </span>
            @endif
        </div>
    </div>

</fieldset>

@section('scripts')
<script type="text/javascript" src="/js/jquery.multiselect.js"> </script>
<script type="text/javascript">

var device_result = $("#device-result-table");

// Device search result
$( "#device-search-button" ).click(function() {
    
    var search_imei = $('#search_imei').val();  
    var job_type = $('#job_type').val();

    $.ajax({  
        url:"/special_case/device",
        //headers: {'X-CSRF-TOKEN': token},
        method:"get",  
        data:{ imei : search_imei},  
        dataType:"text",  
        success:function(data)  
        {  
            device_result.html(data);
            //console.log(imei);
        },
        error: function(data)
        {
            var errors = data.responseJSON;
            console.log(errors); // Render the errors with js
        }  
    });     
});

$('#submitBtn').click(function() {
    if( $("#imei").val() == null ) {
        alert("Please enter the IMEI for device replacement");
        return false;
    } 
});

</script>
@stop