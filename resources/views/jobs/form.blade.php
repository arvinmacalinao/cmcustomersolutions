@inject('states', 'App\Http\Utilities\State')
@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@section('styles')
<link rel="stylesheet" href="/css/jquery.multiselect.css">
@stop

<fieldset id="personalinfo">
    <legend>Device Info</legend>
    <!-- Search Device Registration, than inventories if doesn't exist; prompt link to register -->
    <div class="form-group {{ $errors->has('imei') ? ' has-error' : '' }}">
        {!! Form::label('search_imei', 'IMEI', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            <div class="input-group">
                {!! Form::text('search_imei', old('search_imei'), ['placeholder' => 'Search IMEI...', 'class' => 'form-control', 'required']); !!}
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
    <div id="device-image"></div> 
</fieldset>

<fieldset id="personalinfo">
    <legend>Contact Info</legend>
    <!-- Contact Name & Number Field -->
    <div class="form-group {{ $errors->has('contact_name') ? ' has-error' : '' }}">
        {!! Form::label('contact_name', 'Contact Name', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::text('contact_name', old('contact_name'), array('class' => 'form-control', 'required')); !!}

            @if ($errors->has('contact_name'))
            <span class="help-block">
                <strong>{{ $errors->first('contact_name') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('mobile_number') ? ' has-error' : '' }}">
        {!! Form::label('mobile_number', 'Mobile No.', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::text('mobile_number', old('mobile_number'), array('class' => 'form-control', 'maxlength' => '11')); !!}

            @if ($errors->has('mobile_number'))
            <span class="help-block">
                <strong>{{ $errors->first('mobile_number') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('telephone_number') ? ' has-error' : '' }}">
        {!! Form::label('telephone_number', 'Telephone No.', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::text('telephone_number', old('telephone_number'), array('class' => 'form-control', 'maxlength' => '11')); !!}

            @if ($errors->has('telephone_number'))
            <span class="help-block">
                <strong>{{ $errors->first('telephone_number') }}</strong>
            </span>
            @endif
        </div>
    </div>
</fieldset>

<fieldset id="personalinfo">
    <legend>Job Info</legend>
    <!-- Job Types, Complaints & Remarks -->
    <div class="form-group {{ $errors->has('job_type') ? ' has-error' : '' }}">
        {!! Form::label('job_type', 'Job Type', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::select('job_type', array('1' => 'Body', '2' => 'Accessories'), old('job_type'), ['class' => 'form-control', 'required']); !!} 

            @if ($errors->has('job_type'))
            <span class="help-block">
                <strong>{{ $errors->first('job_type') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div id="bom-list" class="form-group"></div>

    <div class="form-group {{ $errors->has('case_category') ? ' has-error' : '' }}">
        {!! Form::label('case_category', 'Case Category', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::select('case_category', $globalVar::getCaseCategory(), old('case_category'), ['class' => 'form-control', 'required']); !!} 

            @if ($errors->has('case_category'))
            <span class="help-block">
                <strong>{{ $errors->first('case_category') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('job_level_id') ? ' has-error' : '' }}">
        {!! Form::label('job_level_id', 'Job Level', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::select('job_level_id', $job_levels, old('job_level_id'), ['class' => 'form-control', 'required']); !!} 

            @if ($errors->has('job_level_id'))
            <span class="help-block">
                <strong>{{ $errors->first('job_level_id') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('complaint_id') ? ' has-error' : '' }}">
        {!! Form::label('complaint_id', 'Complaints', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            <select name="complaint_id[]" multiple id="complaint_id">
                @foreach($complaint_categories as $index => $category)
                <optgroup label="{{ $category }}">
                    @if(isset($complaint_list[$index]))
                    @foreach($complaint_list[$index] as $complaint)
                    <option value="{{ $complaint['id'] }}">{{ $complaint['name'] }}</option>
                    @endforeach
                    @endif
                </optgroup>
                @endforeach
            </select>

            @if ($errors->has('complaint_id'))
            <span class="help-block">
                <strong>{{ $errors->first('complaint_id') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('note') ? ' has-error' : '' }}">
        {!! Form::label('note', 'Notes', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::textarea('note', old('note'), array('class' => 'form-control', 'maxlength' => '140')); !!}

            @if ($errors->has('note'))
            <span class="help-block">
                <strong>{{ $errors->first('note') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('special_case') ? ' has-error' : '' }}">
        {!! Form::label('special_case', 'Special Case', array('class' => 'col-md-3 control-label')); !!}

        <div class="col-md-7">
            {!! Form::checkbox('special_case', 'true', false, array('disabled' => 'disabled')); !!}

            @if ($errors->has('special_case'))
            <span class="help-block">
                <strong>{{ $errors->first('special_case') }}</strong>
            </span>
            @endif
        </div>
    </div>

    {!! Form::hidden('img', null, array('id' => 'img')); !!}    
</fieldset>


@section('scripts')
<script type="text/javascript" src="/js/jquery.multiselect.js"> </script>
<script type="text/javascript">

var device_result = $("#device-result-table");
var device_image = $("#device-image");

$("#bom-list").hide();

$('#complaint_id').multiselect({
    columns: 2,
    placeholder: 'Select Customer Complaints',
    search: true,
    selectAll: false
});

// Device search result
$( "#device-search-button" ).click(function() {
    
    var search_imei = $('#search_imei').val();  
    var job_type = $('#job_type').val();
    //var token =  $("input[name=_token]").val();
    //var token = document.getElementById('token').value;

    $.ajax({  
        url:"/job/device",
        //headers: {'X-CSRF-TOKEN': token},
        method:"get",  
        data:{ imei : search_imei},  
        dataType:"text",  
        success:function(data)  
        {  
            //$('#device-result-table').show();
            //device_result.fadeOut().html($data).fadeIn();
            device_result.html(data);
            //console.log(imei);
        },
        error: function(data)
        {
            var errors = data.responseJSON;
            console.log(errors);
            // Render the errors with js ...
        }  
    }); 

    // Check whether user select the Job Type to accessories
    if( job_type == 2 && search_imei != null ) {
        $("#bom-list").empty();

        $.ajax({  
            url:"/job/model/accessories",
            //headers: {'X-CSRF-TOKEN': token},
            method:"get",  
            data:{ imei : search_imei},
            dataType:"text",  
            success:function(data)  
            {  
                //bom_list.fadeOut().html($data).fadeIn();
                $("#bom-list").html(data);
                device_image.empty();
            },
            error: function(data)
            {
                var errors = data.responseJSON;
                console.log(errors);
                // Render the errors with js ...
            }  
        });
    } else {
        //Load device img canvas
        $.ajax({  
            url:"/job/device/img",
            method:"get",  
            data:{ imei : search_imei},  
            dataType:"text",  
            success:function(data)  
            {  
                device_image.html(data);
            },
            error: function(data)
            {
                var errors = data.responseJSON;
                console.log(errors);
                // Render the errors with js ...
            }  
        }); 
    }
});

$("#job_level_id").change(function() {
    var job_level_id = $('#job_level_id').val();

    if (job_level_id == 3) {
        $("#special_case").attr("disabled", false);
    } else {
        $("#special_case").prop('checked', false);
        $("#special_case").attr("disabled", true);
    }
})

// Generate accessory list
$("#job_type").change(function() {
    var bom_list = $("#bom-list");
    var job_type = $('#job_type').val();
    var imei = $('input[name=imei]:checked').val();
    var search_imei = $('#search_imei').val(); 
    //var token =  $("input[name=_token]").val();
    //var token = document.getElementById('token').value;
    

    //alert( "IMEI: " + imei + ", Job Type: " + job_type );

    if( job_type == 2 && imei != null ) {
        // User select accessories instead of body.
        // Generate BOM list.
        $("#bom-list").show();
        
        $.ajax({  
            url:"/job/model/accessories",
            //headers: {'X-CSRF-TOKEN': token},
            method:"get",  
            data:{ imei : imei },  
            dataType:"text",  
            success:function(data)  
            {  
                //bom_list.fadeOut().html($data).fadeIn();
                bom_list.html(data);
                device_image.empty();
            },
            error: function(data)
            {
                var errors = data.responseJSON;
                console.log(errors);
                // Render the errors with js ...
            }  
        });

    } else {
        $("#bom-list").hide();
        $("#bom-list").empty();
        
        if (search_imei) {
            // Load device details
            $.ajax({  
                url:"/job/device",
                //headers: {'X-CSRF-TOKEN': token},
                method:"get",  
                data:{ imei : search_imei},  
                dataType:"text",  
                success:function(data)  
                {  
                    //$('#device-result-table').show();
                    //device_result.fadeOut().html($data).fadeIn();
                    device_result.html(data);
                    //console.log(imei);
                },
                error: function(data)
                {
                    var errors = data.responseJSON;
                    console.log(errors);
                    // Render the errors with js ...
                }  
            }); 

            //Load device img canvas
            $.ajax({  
                url:"/job/device/img",
                method:"get",  
                data:{ imei : search_imei},  
                dataType:"text",  
                success:function(data)  
                {  
                    device_image.html(data);
                },
                error: function(data)
                {
                    var errors = data.responseJSON;
                    console.log(errors);
                    // Render the errors with js ...
                }  
            }); 
        };
    }
    
});

$('#submitBtn').click(function() {
    var job_type = $('#job_type').val(); 
    //complaints = $("#complaint_id").length;

    //alert($("#complaint_id").val());
    //return false;

    if( job_type == 2 ) {
        if( $('input:checkbox[name="bom_id[]"]:checked').length < 1 ) {
            alert("You must select at least 1 accessory.");
            return false;
        }
    }

    if( $("#complaint_id").val() == null ) {
        alert("You must select at least 1 complaint.");
        return false;
    }
});

</script>
@stop