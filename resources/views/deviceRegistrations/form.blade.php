@inject('param', 'App\Http\Utilities\GlobalConstant')

<fieldset id="personalinfo">
    <legend>Device Info</legend>
    <div class="form-group {{ $errors->has('imei') ? ' has-error' : '' }}">
        {!! Form::label('search_imei', 'IMEI', array('class' => 'col-md-2 control-label')); !!}

        <div class="col-md-10">
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
</fieldset>

<fieldset id="personalinfo">
    <legend>Customer Info</legend>
    <div class="form-group {{ $errors->has('customer_id') ? ' has-error' : '' }}">
        {!! Form::label('search_customer', 'Customer', array('class' => 'col-md-2 control-label')); !!}

        <div class="col-md-10">
            <div class="col-md-2">
                {!! Form::select('category', ['name' => 'Name', 'email' => 'Email', 'mobile_number' => 'Mobile No.'], null, ['class' => 'form-control', 'required']); !!}
            </div>
            
            <div class="input-group">
                {!! Form::text('search_customer', old('search_customer'), ['placeholder' => 'Search Customer...', 'class' => 'form-control', 'required']); !!}
                <span class="input-group-btn">
                    <button id="customer-search-button" class="btn btn-default" type="button">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>
                </span>
            </div>

            @if ($errors->has('customer_id'))
            <span class="help-block">
                <strong>{{ $errors->first('customer_id') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div id="customer-result-table"></div> 
</fieldset>

<fieldset id="personalinfo">
    <legend>Registration Details</legend>
    <div class="form-group {{ $errors->has('pop_ref') ? ' has-error' : '' }}">
        {!! Form::label('pop_ref', 'Invoice Ref. No.', array('class' => 'col-md-2 control-label')); !!}

        <div class="col-md-10">
            {!! Form::text('pop_ref', old('pop_ref'), array('class' => 'form-control', 'maxlength' => '100', 'required')); !!}

            @if ($errors->has('pop_ref'))
                <span class="help-block">
                <strong>{{ $errors->first('pop_ref') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group {{ $errors->has('pop_date') ? ' has-error' : '' }}">
        {!! Form::label('pop_date', 'Purchase Date', array('class' => 'col-md-2 control-label')); !!}

        <div class="col-md-10">
            <div class='input-group date'>
                {!! Form::date('pop_date', old('pop_date'), ['placeholder' => 'Purchase Date...', 'class' => 'form-control', 'required']); !!}
                <span class="input-group-addon">
                    <span class="fa fa-calendar"></span>
                </span>
            </div>

            @if ($errors->has('pop_date'))
            <span class="help-block">
                <strong>{{ $errors->first('pop_date') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('warranty_status', 'Warranty Status', array('class' => 'col-md-2 control-label')); !!}

        <div class="col-md-10">
            {!! Form::checkbox('warranty_status', '3', old('warranty_status')); !!} Void Warranty

            @if ($errors->has('warranty_status'))
                <span class="help-block">
                <strong>{{ $errors->first('warranty_status') }}</strong>
            </span>
            @endif
        </dev>
    </div>
</fieldset>


@section('scripts')
    <script type="text/javascript">
    //$('#device-result-table').hide();

    // Date feature
    $( function() {
        var currentDate = $('#pop_date').val();

        $( "#pop_date" ).datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "-110:+0",
            dateFormat: 'yy-mm-dd',
            setDate: currentDate,
        });
    } );

    // Device search result
    $( "#device-search-button" ).click(function() {
        var device_result = $("#device-result-table");
        var search_imei = $('#search_imei').val(); 

        $.ajax({  
            url:"/device_registration/device",
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
                console.log(errors);
            }  
        }); 
    });

    // Customer search result
    $( "#customer-search-button" ).click(function() {
        var customer_result = $("#customer-result-table");
        var search_customer = $('#search_customer').val();
        var category = $('select[name=category]').val();
        var token =  $("input[name=_token]").val();

        $.ajax({  
            url:"/device_registration/customer",
            //headers: {'X-CSRF-TOKEN': token},
            method:"get",  
            data:{ category: category, search: search_customer},  
            dataType:"text",  
            success:function(data)  
            {
                customer_result.html(data);
                //console.log(search_customer);
            },
            error: function(data)
            {
                var errors = data.responseJSON;
                console.log(errors);
                // Render the errors with js ...
            }  
        }); 
    });

    </script>
@stop


