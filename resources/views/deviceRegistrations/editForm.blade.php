@inject('param', 'App\Http\Utilities\GlobalConstant')

<fieldset id="personalinfo">
    <legend>Device Info</legend>
    <div class="form-group {{ $errors->has('imei') ? ' has-error' : '' }}">
        {!! Form::label('imei', 'IMEI', array('class' => 'col-md-2 control-label')); !!}

        <div class="col-md-10">
            {!! Form::text('imei', old('imei'), ['class' => 'form-control', 'disabled']); !!}
            {!! Form::hidden('imei', old('imei')); !!}

            @if ($errors->has('imei'))
                <span class="help-block">
                <strong>{{ $errors->first('imei') }}</strong>
            </span>
            @endif
        </div>
    </div>
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
                {!! Form::text('search_customer', $device->customer->name, ['placeholder' => 'Search Customer...', 'class' => 'form-control', 'required']); !!}
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

    <div id="customer-result-table">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Gender</th>
                    <th>Mobile Number</th>
                    <th>ID Type</th>
                    <th>ID Number</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{!! Form::radio('customer_id', $device->customer->id, true); !!}</td>
                    <td>{{ $device->customer->name }}</td>
                    <td>{{ $device->customer->email }}</td>
                    <td>{{ $device->customer->gender }}</td>
                    <td>{{ $device->customer->mobile_number }}</td>
                    @if( $device->customer->id_type )
                    <td>{{ $param::getCustomerIDType()[$device->customer->id_type] }}</td>
                    @else
                    <td></td>
                    @endif
                    <td>{{ $device->customer->id_number }}</td>
                </tr>
            </tbody>
        </table>
    </div> 
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

    <div class="form-group {{ $errors->has('warranty_date') ? ' has-error' : '' }}">
        {!! Form::label('warranty_date', 'Warranty Date', array('class' => 'col-md-2 control-label')); !!}

        <div class="col-md-10">
            <div class='input-group date'>
                {!! Form::date('warranty_date', old('warranty_date'), ['placeholder' => 'Warranty Date...', 'class' => 'form-control', 'required']); !!}
                <span class="input-group-addon">
                    <span class="fa fa-calendar"></span>
                </span>
            </div>

            @if ($errors->has('warranty_date'))
            <span class="help-block">
                <strong>{{ $errors->first('warranty_date') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('warranty_status', 'Warranty', array('class' => 'col-md-2 control-label')); !!}

        <div class="col-md-10">
            @if( old('imei') == 3 || $device->warranty_status == 3 )
            {!! Form::checkbox('warranty_status', 3, true); !!} Void Warranty 
            @else 
            {!! Form::checkbox('warranty_status', 3, false); !!} Void Warranty 
            @endif

            @if ($errors->has('warranty_status'))
                <span class="help-block">
                <strong>{{ $errors->first('warranty_status') }}</strong>
            </span>
            @endif
        </div>
    </div>
    <!-- endif -->
</fieldset>


@section('scripts')
    <script type="text/javascript">
    //$('#device-result-table').hide();

    // Date feature
    $( function() {
        var currentDate = $('#pop_date').val();
        var warrantyDate = $('#warranty_date').val();

        $( "#pop_date" ).datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "-110:+0",
            dateFormat: 'yy-mm-dd',
            setDate: currentDate,
        });

        $( "#warranty_date" ).datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "-110:+10",
            dateFormat: 'yy-mm-dd',
            setDate: warrantyDate,
        });
    } );

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
                console.log(search_customer);
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


