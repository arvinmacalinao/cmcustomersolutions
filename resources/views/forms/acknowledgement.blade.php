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

    ol.tnc {
      font-size: 10px;
    }

    div.company-column {
      font-size: 10px;
      padding: 0;
    }

    table.signature {
      margin-bottom: 10px;
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
        <td width="300"><h3>Acknowledgement Form</h3></td>
        <td>Date</td>
        <td width="200">: {{ $job->created_at }}</td>
      </tr>
    </table>

    <hr>

    <table width="100%">
      <tr>
        <td>Job No.</td>
        <td>: <b>{{ sprintf('JO%08d', $job->id) }}</b></td>
        <td>Purchase Date</td>
        <td>: {{ $job->device->pop_date }}</td>
      </tr>
      <tr>
        <td>Customer</td>
        <td>: {{ $job->contact_name }}</td>
        <td>Company Name</td>
        <td>: {{ $job->company->company_name }}</td>
      </tr>
      <tr>
        <td>Case Category</td>
        <td>: {{ $globalVar::getCaseCategory()[$job->case_category] }}</td>
        <td>Invoice No.</td>
        <td>: 
        @if( $job->device->pop_ref )
          {{ $job->device->pop_ref }}
        @else
          -
        @endif
        </td>
      </tr>
      <tr>
        <td>IMEI</td>
        <td>: <b>{{ $job->imei }}</b></td>
        <td>Customer Service</td>
        <td>: {{ $job->creator->name }}</td>
      </tr>
      <tr>
        <td>Warranty Status</td>
        <td>: {{ $globalVar::getWarrantyStatus()[$job->warranty] }}</td>
        <td>Contact Number</td>
        <td>: {{ $job->mobile_number }}</td>
      </tr>
      <tr>
        <td>Dealer</td>
        <td>: -</td>
        <td>Email</td>
        <td>: {{ $job->device->customer->email }}</td>
      </tr>
      <tr>
        <td>Origin</td>
        <td>: {{ $job->company->company_name }}</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>

    <hr>

    <table width="100%">
      <tr>
        <td width="70">Model</td>
        <td>: {{ $job->device->inventory->model->code }}</td>
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
          <p class="checkmark">LEGEND: X - CRACK &nbsp; O - DENTS &nbsp; &#10003; - SCRATCHES</p>
        </td>
      </tr>
      <tr>
        <td>Color</td>
        <td>: {{ $job->device->inventory->color }}</td>
      </tr>
      <tr>
        <td>Accessories</td>
        <td>: 
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
        </td>
      </tr>
      <tr>
        <td>Complaint</td>
        <td>: 
            @foreach($job->complaints as $complaint)
              @if ($complaint == $job->complaints->last())
              {{ $complaint->name }}
              @else
              {{ $complaint->name }}, 
              @endif
            @endforeach
        </td>
      </tr>
      <tr>
        <td>CS Notes</td>
        <td>: {{ $job->note }}</td>
      </tr>
    </table>

    <hr>

    <h5>General Terms and Conditions</h5>
    <ol class="tnc">
      <li>
        Cherry Mobile Customer Solutions reserves the right to accept or refuse the unit under the following circumstances:</li>
        <ol type="a">
          <li>Unauthorized opening or repair of the unit</li>
          <li>Damages caused by the customer’s negligence</li>
        </ol>
        <li>For the warranty units, replaced faulty parts will not be returned to the customer.</li>
        <li>The company does not provide back-up services during repair. Customers should have back-up copies of all important data stored in their units.</li>
        <li>Cherry Mobile Customer Solutions reserves the right to dispose unclaimed or unpaid units three (3) months from the date the customer was informed of his/her repaired unit/s.</li>
        <li>For OUT of WARRANTY handsets launched 2 years and beyond. Cherry Mobile Customer Solutions has an option not to accept the unit for repair or to return the unit due to unavailability of the parts.</li>
        <li>For VOID and out-of-warranty units, the company may charge a fee corresponding to the repair of defective or damaged units.</li>
        <li>Once a service request has been processed, and yet the customer decides to withdraw his/her unit, a pull out fee of Php 150.00 will be charged to the customer.</li>
        <li>In case of lost Acknowledgement Form, the customer must present a notarized Affidavit of Loss to claim his/her unit.</li>
        <li>If another person will claim the repaired unit other than the customer, he/she must present an Authorization Letter together with two (2) valid IDs.</li>
    </ol>
    <p>I hereby agree to the terms and conditions as set by Cherry Mobile Customer Solutions and give the company my full consent to conduct the necessary repair and/or service to my mobile unit.</p>

    <table class="table borderless signature">
      <tr>
        <td align="center" width="45%">
          <br/>
          <hr style="margin-bottom: 0em; border-width: 2px;">
          <p>Client Signature Over Printed Name/Date</p>
        </td>
        <td width="10%">
          &nbsp;
        </td>
        <td align="center" width="45%">
          <br/>
          <hr style="margin-bottom: 0em; border-width: 2px;">
          <p>CSR Name Over Printed Name/Date</p>
        </td>
      </tr>
    </table>

    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-info">
          <div class="panel-body company-column" align="center">
            <h5>{{ $job->company->company_name }}</h5>
            <p>Address: {{ $job->company->address }}</p>
            <p>Tel: {{ $job->company->contact_number }} &nbsp;&nbsp;&nbsp; Fax: {{ $job->company->fax_number }}</p>
          </div>
        </div>
      </div>
    </div>
</div>

@stop