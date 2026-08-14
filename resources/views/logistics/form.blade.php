@inject('states', 'App\Http\Utilities\State')

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Delivery Order Details</h3>
    </div>
    
    <div class="panel-body">
        <div class="form-group col-md-6 {{ $errors->has('waybill_number') ? ' has-error' : '' }}">
            {!! Form::label('waybill_number', 'Waybill No.', array('class' => 'control-label col-md-3')); !!}
            <div class="col-md-9">
                {!! Form::text('waybill_number', old('waybill_number'), array('class' => 'form-control', 'required')); !!}

                @if ($errors->has('waybill_number'))
                    <span class="help-block">
                    <strong>{{ $errors->first('waybill_number') }}</strong>
                </span>
                @endif
            </div>
        </div>

        <div class="form-group col-md-6 {{ $errors->has('attention_to') ? ' has-error' : '' }}">
            {!! Form::label('attention_to', 'Attention To', array('class' => 'control-label col-md-3')); !!}
            <div class="col-md-9">
                {!! Form::text('attention_to', old('attention_to'), array('class' => 'form-control', 'required')); !!}

                @if ($errors->has('attention_to'))
                    <span class="help-block">
                    <strong>{{ $errors->first('attention_to') }}</strong>
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="form-group col-md-6 {{ $errors->has('email') ? ' has-error' : '' }}">
            {!! Form::label('email', 'Email', array('class' => 'control-label col-md-3')); !!}
            <div class="col-md-9">
                {!! Form::text('email', old('email'), array('class' => 'form-control', 'required')); !!}

                @if ($errors->has('email'))
                    <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
            </div>
        </div>

        <div class="form-group col-md-6 {{ $errors->has('contact_number') ? ' has-error' : '' }}">
            {!! Form::label('contact_number', 'Contact No.', array('class' => 'control-label col-md-3')); !!}
            <div class="col-md-9">
                {!! Form::text('contact_number', old('contact_number'), array('class' => 'form-control', 'required')); !!}

                @if ($errors->has('contact_number'))
                    <span class="help-block">
                    <strong>{{ $errors->first('contact_number') }}</strong>
                </span>
                @endif
            </div>
        </div>
    </div>

    @if($user_company_id == 1)
    <div class="panel-body">
        <div class="form-group col-md-6 {{ $errors->has('company_to') ? ' has-error' : '' }}">
            {!! Form::label('company_to', 'Route To', array('class' => 'control-label col-md-3')); !!}
            <div class="col-md-9">
                {!! Form::select('company_to', $companies, old('company_to'), ['class' => 'form-control', 'id' => 'company_to', 'placeholder' => 'Select a Company...']); !!}

                @if ($errors->has('company_to'))
                    <span class="help-block">
                    <strong>{{ $errors->first('company_to') }}</strong>
                </span>
                @endif
            </div>
        </div>

        <div class="form-group col-md-6 {{ $errors->has('address') ? ' has-error' : '' }}">
            {!! Form::label('address', 'Address', array('class' => 'control-label col-md-3')); !!}
            <div class="col-md-9">
                {!! Form::text('address', old('address'), array('class' => 'form-control', 'required')); !!}

                @if ($errors->has('address'))
                    <span class="help-block">
                    <strong>{{ $errors->first('address') }}</strong>
                </span>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="panel-body">
        <div class="form-group col-md-6 {{ $errors->has('remark') ? ' has-error' : '' }}">
            {!! Form::label('remark', 'Remark', array('class' => 'control-label col-md-3')); !!}
            <div class="col-md-9">
                {!! Form::text('remark', old('remark'), array('class' => 'form-control', 'required')); !!}

                @if ($errors->has('remark'))
                    <span class="help-block">
                    <strong>{{ $errors->first('remark') }}</strong>
                </span>
                @endif
            </div>
        </div>

        <div class="form-group col-md-6 {{ $errors->has('address') ? ' has-error' : '' }}">
            {!! Form::label('address', 'Address', array('class' => 'control-label col-md-3')); !!}
            <div class="col-md-9">
                {!! Form::text('address', old('address'), array('class' => 'form-control', 'required')); !!}

                @if ($errors->has('address'))
                    <span class="help-block">
                    <strong>{{ $errors->first('address') }}</strong>
                </span>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="panel-body">
        <div class="form-group col-md-6 {{ $errors->has('postcode') ? ' has-error' : '' }}">
            {!! Form::label('postcode', 'Postcode', array('class' => 'control-label col-md-3')); !!}
            <div class="col-md-9">
                {!! Form::text('postcode', old('postcode'), array('class' => 'form-control')); !!}

                @if ($errors->has('postcode'))
                    <span class="help-block">
                    <strong>{{ $errors->first('postcode') }}</strong>
                </span>
                @endif
            </div>
        </div>
        
        <div class="form-group col-md-6 {{ $errors->has('state_id') ? ' has-error' : '' }}">
            {!! Form::label('state_id', 'State', array('class' => 'control-label col-md-3')); !!}
            <div class="col-md-9">
                {!! Form::select('state_id', $states::all(), old('state_id'), ['class' => 'form-control', 'placeholder' => 'Select a State...', 'required']); !!}

                @if ($errors->has('state_id'))
                    <span class="help-block">
                    <strong>{{ $errors->first('state_id') }}</strong>
                </span>
                @endif
            </div>
        </div>
    </div>

    @if($user_company_id == 1)
    <div class="panel-body">
        <div class="form-group col-md-6 {{ $errors->has('remark') ? ' has-error' : '' }}">
            {!! Form::label('remark', 'Remark', array('class' => 'control-label col-md-3')); !!}
            <div class="col-md-9">
                {!! Form::text('remark', old('remark'), array('class' => 'form-control', 'required')); !!}

                @if ($errors->has('remark'))
                    <span class="help-block">
                    <strong>{{ $errors->first('remark') }}</strong>
                </span>
                @endif
            </div>
        </div>
    </div>
    @endif

</div>

<div class="panel panel-default">
    <div id="job-result-table">
        @if($user_company_id == 1)
            @include('logistics.jobLogistic', ['some' => 'data'])
        @else
            @include('logistics.jobList', ['some' => 'data'])
        @endif
    </div> 
</div>


@section('scripts')
    <script type="text/javascript">

    $("form#form-create-logistic").submit(function( event ) {
        checked = $("input[type=checkbox]:checked").length;

        if( !checked ) {
            //alert({!! trans('cdu.err_select_item', ['item' => 'job']) !!});
            alert('You must select at least 1 job for shipment.');
            return false;
        }
    });

    // Customer search result
    $( "#company_to" ).change(function() {

        var job_result_tbl = $("#job-result-table");
        var company_to = $( "#myselect" ).val();
        var user_company_id = $('select[name=category]').val();
        var token =  $("input[name=_token]").val();

        $.ajax({  
            url:"/logistic/job",
            //headers: {'X-CSRF-TOKEN': token},
            method:"get",  
            data:{ user_company_id: user_company_id, ship_route_id: company_to},  
            dataType:"text",  
            success:function(data)  
            {
                job_result_tbl.html(data);
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