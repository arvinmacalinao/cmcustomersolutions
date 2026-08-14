@section('scripts')
	<!-- <link rel="stylesheet" type="text/css" media="print" href="bootstrap.min.css"> -->
	<!-- <link rel="stylesheet" type="text/css" href="/css/form.css"> -->
  <style>

    .borderless tbody tr td {
      border: none;
    }

    div.container {
      font-size: 11px;
    }

    div.company-column {
      font-size: 10px;
      padding: 0;
    }

    table.signature {
      margin-bottom: 5px;
    }

  </style>
@stop

@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@extends('forms.layout')

@section('content')
  <div class="container">
    <table width="100%">
      <tr>
        <td colspan="4"><img src="{{ url('/images/cherry_mobile_logo_header.png') }}" width="220"></td>
      </tr>
      <tr>
        <td width="300"><h3>Job Sheet Report</h3></td>
        <td>Date</td>
        <td width="200">: <b>{{ $job->created_at }}</b></td>
      </tr>
    </table>

    <hr>

    <table width="100%">
      <tr>
        <td>Job No.</td>
        <td>: <b>{{ sprintf('JO%08d', $job->id) }}</b></td>
        <td>Purchase Date</td>
        <td>: <b>{{ $job->device->pop_date }}</b></td>
      </tr>
      <tr>
        <td>Customer</td>
        <td>: <b>{{ $job->contact_name }}</b></td>
        <td>Company Name</td>
        <td>: <b>{{ $job->company->company_name }}</b></td>
      </tr>
      <tr>
        <td>Case Category</td>
        <td>: <b>{{ $globalVar::getCaseCategory()[$job->case_category] }}</b></td>
        <td>Invoice No.</td>
        <td>: 
        <b>
        @if( $job->device->pop_ref )
          {{ $job->device->pop_ref }}
        @else
          -
        @endif
   		 </b>
        </td>
      </tr>
      <tr>
        <td>IMEI</td>
        <td>: <b>{{ $job->imei }}</b></td>
        <td>Services</td>
        <td>: <b>{{ $job->services }}</b></td>
      </tr>
      <tr>
        <td>Warranty Status</td>
        <td>: <b>{{ $globalVar::getWarrantyStatus()[$job->warranty] }}</b></td>
        <td>Contact Number</td>
        <td>: <b> {{ $job->mobile_number }}</b></td>
      </tr>
      <tr>
        <td>Dealer</td>
        <td>: -</td>
        <td>Email</td>
        <td>: <b>{{ $job->device->customer->email }}</b></td>
      </tr>
      <tr>
        <td>Origin</td>
        <td>: <b>{{ $job->company->company_name }}</b></td>
        <td>Customer Service</td>
        <td>: <b>{{ $job->creator->name }}</b></td>
      </tr>
    </table>

    <hr>

    <table width="100%">
      <tr>
        <td width="70">Model</td>
        <td>:<b> {{ $job->device->inventory->model->code }}</b></td>
        <td rowspan="5" width="150">
          @if($job->image)
            <img src="{{ url('/images/job/' . $job->image) }}" height="200">
          @else
            @if ($job->device->inventory->model->device_type_id == 1)
            <img src="{{ url('/images/feature_phone.png') }}" height="200">
            @elseif ($job->device->inventory->model->device_type_id == 2)
            <img src="{{ url('/images/smart_phone.png') }}" height="200">
            @else
            <img src="{{ url('/images/tablet.png') }}" height="200">
            @endif
          @endif
        </td>
      </tr>
      <tr>
        <td>Color</td>
        <td>: <b>{{ $job->device->inventory->color }}</b></td>
      </tr>
      <tr>
        <td>Accessories</td>
        <td>: 
        <b>
            @if($job->accessories)
            @foreach($job->accessories as $accessory)
              @if ($accessory == $job->accessories->last())
              {{ $accessory->name }}
              @else
              {{ $accessory->name }}, 
              @endif
            @endforeach
            @else
            -
            @endif
        </b>
        </td>
      </tr>
      <tr>
        <td>Complaint</td>
        <td>: 
        	<b>
            @foreach($job->complaints as $complaint)
              @if ($complaint == $job->complaints->last())
              {{ $complaint->name }}
              @else
              {{ $complaint->name }}, 
              @endif
            @endforeach
        </b>
        </td>
      </tr>
      <tr>
        <td>CS Notes</td>
        <td>: <b>{{ $job->note }}</b></td>
      </tr>
    </table>

    <hr>

    <h5>Service Agreement</h5>
    <p>I hereby agree to the terms and conditions set by Cherry Mobile Customer Solutions and give my full consent for the company to conduct the necessary repair and/or service to my device.</p>
    <table class="table borderless">
      <tr>
        <td width="280">&nbsp;</td>
        <td>
          <b>Agreed By:</b>
          <br><br><br><br>
          [Customer Signature Over Printed Name]
        </td>
      </tr>
    </table>

    <h5>Technical Use</h5>
    <table class="table borderless">
      <tr>
        @if( $job->technicals->last() )
          <td width="100">Technician Sign In Date</td>
          <td>: {{ $job->technicals->last()->acceptance_date }}</td>
          <td width="100">Technician Sign Out Date</td>
          <td>: {{ $job->technicals->last()->completion_date }}</td>
        @else
          <td width="100">Technician Sign In Date</td>
          <td>: -</td>
          <td width="100">Technician Sign Out Date</td>
          <td>: -</td>        
        @endif
      </tr>
    </table>

    <table width="100%">
      <tr>
        <td>No.</td>
        <td>Part #</td>
        <td>Part Name</td>
        <td>Qty</td>
        <td>SR #</td>
        <td>Remarks</td>
      </tr>
      <tr>
        <td>1</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </table>
    
    <br/>

    <table width="100%">
      <tr>
        <td width="150">Repair Level</td>
        <td>: {{ $job->job_level_id }}</td>
      </tr>
      <tr>
        <td width="150">Technician Remarks</td>
        @if( $job->technicals->last() )
          <td>
            : 
            @if($job->technicals->last()->remark)
              {{ $job->technicals->last()->remark }},
            @endif

            @if($job->technicals->last()->remarks)
                @foreach($job->technicals->last()->remarks as $remark)
                  @if ($remark == $job->technicals->last()->remarks->last())
                  {{ $remark->name }}
                  @else
                  {{ $remark->name }}, 
                  @endif
                @endforeach
            @endif
          </td>
        @else
        <td>: -</td>
        @endif
      </tr>
    </table>

    <br>
    <p>Customer Certification</p>
    <p>I acknowledge receipt of the above unit and certify that the above service has been released to my satisfaction.</p>
    <table class="table borderless signature">
      <tr>
        <td width="70">Received By:</td>
        <td align="center">
          <br/>
          <hr style="margin-bottom: 0em; border-width: 2px;">
          <p>Customer Signature</p>
        </td>
        <td width="70">Released By:</td>
        <td align="center">
          <br/>
          <hr style="margin-bottom: 0em; border-width: 2px;">
          <p>Customer Service Signature</p>
        </td>
      </tr>
    </table>

      <!-- TODO: the first tr section of this table is causing the problem
      Seems to be an overflow problem, if reduce to 1 tr no problem -->
    <table width="100%">
      <tr>
        <td width="80">Delivery Order#:</td>
        {{-- @if( $job->logistic->first() )
        <td>{{ sprintf('DO%08d', $job->logistic->first()->logistic->id) }}</td>
        @else
        <td>-</td>
        @endif --}}
        <td>-</td>
        <td width="80">Cash Sale#:</td>
        <td>-</td>
      </tr>
      <tr>
        <td width="80">Invoice#:</td>
        <td>
        -
        </td>
        <td width="80">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table> 

    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-info">
          <div class="panel-body company-column" align="center">
            <h3>{{ $job->company->company_name }}</h3>
            <p>Address: {{ $job->company->address }}</p>
            <p>Tel: {{ $job->company->contact_number }} &nbsp;&nbsp;&nbsp; Fax: {{ $job->company->fax_number }}</p>
          </div>
        </div>
      </div>
    </div>
</div>

@stop