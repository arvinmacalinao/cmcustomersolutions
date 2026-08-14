@inject('states', 'App\Http\Utilities\State')
@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@section('styles')
<link rel="stylesheet" href="/css/jquery.multiselect.css">
@stop

<fieldset id="personalinfo">
    <legend>Job Details</legend>
    <!-- Search Device Registration, than inventories if doesn't exist; prompt link to register -->
    <div class="form-group {{ $errors->has('imei') ? ' has-error' : '' }}">
        {!! Form::label('search_imei', 'IMEI', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            <div class="input-group">
                {!! Form::text('search_imei', old('search_imei'), ['placeholder' => 'Search IMEI...', 'class' => 'form-control', 'required']); !!}
                <span class="input-group-btn">
                    <button id="job-search-button" class="btn btn-default" type="button">
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

    <div id="job-result-table"></div> 
</fieldset>

<fieldset id="personalinfo">
    <legend>Ticket Details</legend>
    <div class="form-group {{ $errors->has('type') ? ' has-error' : '' }}">
        {!! Form::label('type', 'Ticket Type', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::select('type', $globalVar::getTicketType(), old('type'), ['class' => 'form-control', 'required']); !!}

            @if ($errors->has('type'))
            <span class="help-block">
                <strong>{{ $errors->first('type') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('customer_name') ? ' has-error' : '' }}">
        {!! Form::label('customer_name', 'Customer Name', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::text('customer_name', old('customer_name'), array('class' => 'form-control', 'maxlength' => '45', 'required')); !!}

            @if ($errors->has('customer_name'))
            <span class="help-block">
                <strong>{{ $errors->first('customer_name') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('customer_contact') ? ' has-error' : '' }}">
        {!! Form::label('customer_contact', 'Contact No.', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::text('customer_contact', old('customer_contact'), array('class' => 'form-control', 'maxlength' => '11', 'required')); !!}

            @if ($errors->has('customer_contact'))
            <span class="help-block">
                <strong>{{ $errors->first('customer_contact') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
        {!! Form::label('description', 'Description', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::textarea('description', old('description'), array('class' => 'form-control', 'required')); !!}

            @if ($errors->has('description'))
            <span class="help-block">
                <strong>{{ $errors->first('description') }}</strong>
            </span>
            @endif
        </div>
    </div>
</fieldset>


@section('scripts')
<script type="text/javascript" src="/js/jquery.multiselect.js"> </script>
<script type="text/javascript">

var job_result = $("#job-result-table");

// Device search result
$( "#job-search-button" ).click(function() {
    
    var search_imei = $('#search_imei').val();

    $.ajax({  
        //url:"/job/device",
        url:"/job/api/job/info",
        //headers: {'X-CSRF-TOKEN': token},
        method:"get",  
        data:{ imei : search_imei },  
        dataType:"text",  
        success:function(data)  
        {
            job_result.html(data);
            //console.log(imei);
        },
        error: function(data)
        {
            var errors = data.responseJSON;
            alert(errors);
            console.log(errors);
        }  
    }); 
});

</script>
@stop