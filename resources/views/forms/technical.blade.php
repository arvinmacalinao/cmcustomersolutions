@section('scripts')
	<!-- <link rel="stylesheet" type="text/css" media="print" href="bootstrap.min.css"> -->
	<!-- <link rel="stylesheet" type="text/css" href="/css/form.css"> -->
  <style>
    .borderless tbody tr td {
      border: none;
    }
  </style>
@stop

@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@extends('forms.layout')

@section('content')
  <div class="container" style="margin-bottom: 10px;">
    <table width="100%" style="margin-bottom: 20px;">
      <tr align="center">
        <td><img src="{{ url('/images/cherry_mobile_logo_header.png') }}" width="250"></td>
      </tr>
    </table>

    <table width="100%" style="margin-bottom: 5px; border: 1px solid black;">
      <tr>
        <td width="60%" style="background-color:black; color:white;"><h4>&nbsp;TECHNICAL REPORT</h4></td>
        <td style="background-color:black; color:white;">Document No.</td>
        <td>&nbsp;{{ sprintf('JB%08d', $technical->id) }} </td>
      </tr>
    </table>

    <p style="background-color: #d3d3d3;"><b>Customer Information</b></p>

    <table width="100%" style="margin: 10px 0px;font-size: 12px">
      <tr>
        <td>Name</td>
        <td>: {{ $technical->job->contact_name }}</td>
      </tr>
      <tr>
        <td>Address</td>
        <td>: {{ $technical->job->device->customer->address }}</td>
      </tr>
      <tr>
        <td>Tel No.</td>
        <td>: {{ $technical->job->contact_number }}</td>
      </tr>
      <tr>
        <td>E-mail</td>
        <td>: {{ $technical->job->device->customer->email }}</td>
      </tr>
      <tr>
        <td>Mobile</td>
        <td>: {{ $technical->job->mobile_number }}</td>
      </tr>
    </table>

    <p style="background-color: #d3d3d3;"><b>Unit Information</b></p>

    <table width="100%" style="margin: 10px 0px;font-size: 12px">
      <tr>
        <td>Model</td>
        <td>: {{ $technical->job->device->inventory->model->name }}</td>
        <td>JO No.</td>
        <td>: {{ sprintf('JO%08d', $technical->job->id) }}</td>
      </tr>
      <tr>
        <td>Date of Purchase</td>
        <td>: {{ $technical->job->device->pop_date }}</td>
        <td>Warranty Status</td>
        <td>: {{ $globalVar::getWarrantyStatus()[$technical->job->device->warranty_status] }}</td>
      </tr>
      <tr>
        <td>Name of Dealer</td>
        <td colspan="3">: {{ $technical->technician->company->company_name }}</td>
      </tr>
      <tr>
        <td>Color</td>
        <td colspan="3">: {{ $technical->job->device->inventory->color }}</td>
      </tr>
      <tr>
        <td>IMEI No.</td>
        <td colspan="3">: {{ $technical->job->imei }}</td>
      </tr>
      <tr>
        <td>CSR Remark</td>
        <td colspan="3">: {{ $technical->job->note }}</td>
      </tr>
      <tr>
        <td>Complaint</td>
        <td colspan="3">: 
          @if($technical->job->complaints)
          @foreach($technical->job->complaints as $complaint)
            @if ($complaint == $technical->job->complaints->last())
            {{ $complaint->name }}
            @else
            {{ $complaint->name }}, 
            @endif
          @endforeach
          @endif
        </td>
      </tr>
      <tr>
        <td>Tech Remark</td>
        <td colspan="3">: 
          @if($technical->remark)
            {{ $technical->remark }}, 
          @endif

          @if($technical->remarks)
            @foreach($technical->remarks as $remark)
              @if ($remark == $technical->remarks->last())
              {{ $remark->name }}
              @else
              {{ $remark->name }}, 
              @endif
            @endforeach
          @endif
        </td>
      </tr>
    </table>

    <hr>

    <table width="100%">
      <tr>
        <td align="left" width="45%">
          Prepared by:
          <br/><br/><br/>
        </td>
        <td width="10%">
          &nbsp;
        </td>
        <td align="left" width="45%">
          Diagnosed and handled by:
          <br/><br/><br/>
        </td>
      </tr>
      <tr>
        <td align="center" width="45%">
          <hr style="margin-bottom: 0em; border-width: 2px;">
          <p>CSR Signature Over Printed Name/Date</p>
        </td>
        <td width="10%">
          &nbsp;
        </td>
        <td align="center" width="45%">
          <hr style="margin-bottom: 0em; border-width: 2px;">
          <p>TSR Signature Over Printed Name/Date</p>
        </td>
      </tr>
      <tr>
        <td align="left" width="45%">
          <br/>
          Noted by:
          <br/><br/><br/>
        </td>
        <td width="10%">
          &nbsp;
        </td>
        <td align="left" width="45%">
          &nbsp;
        </td>
      </tr>
      <tr>
        <td align="center" width="45%">
          <hr style="margin-bottom: 0em; border-width: 2px;">
          <p>Manager Signature Over Printed Name/Date</p>
        </td>
        <td width="10%">
          &nbsp;
        </td>
        <td align="center" width="45%">
          &nbsp;
        </td>
      </tr>
    </table>

    {{-- 
    

    <h3>Service Agreement</h3>
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

    <h3>Technical Use</h3>
    <table class="table borderless">
      <tr>
        <td>Technician Sign In Date</td>
        <td>: -</td>
        <td>Technician Sign Out Date</td>
        <td>: -</td>
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
        <td>: -</td>
      </tr>
      <tr>
        <td width="150">Technician Remarks</td>
        <td>: -</td>
      </tr>
    </table>

    <br>
    <p>Customer Certification</p>
    <p>I acknowledge receipt of the above unit and certify that the above service has been released to my satisfaction.</p>
    <table class="table borderless">
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

    <table width="100%">
      <tr>
        <td width="80">Delivery Order#:</td>
        <td>-</td>
        <td width="80">Cash Sale#:</td>
        <td>-</td>
      </tr>
      <tr>
        <td width="80">Invoice#:</td>
        <td>-</td>
        <td width="80">&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>
    --}}

    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-info">
          <div class="panel-body" align="center">
            <h4>{{ $technical->technician->company->company_name }}</h3>
            <p>Address: {{ $technical->technician->company->address }}</p>
            <p>Tel: {{ $technical->technician->company->contact_number }} &nbsp;&nbsp;&nbsp; Fax: {{ $technical->technician->company->fax_number }}</p>
          </div>
        </div>
      </div>
    </div>
</div>

@stop