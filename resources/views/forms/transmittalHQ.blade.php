@section('scripts')
<style>
	/* .borderless tbody tr td {
		border: 1;
	} */

	table, th, td {
	   border: 1px solid black;
	   padding: 1px;
	   font-size: 9px;
	}

	table.borderless, table.borderless tr td {
      border: none;
    }

</style>
@stop

@inject('param', 'App\Http\Utilities\GlobalConstant')

@extends('forms.layout')

@section('content')
<div class="container"  >
	<table width="100%" class="borderless" >
		<tr>
			<td><img src="{{ url('/images/cherry_mobile_logo_header.png') }}" width="250"></td>
			<td align="right">
				<b>Transmittal Form</b><br>
				{{ $logistic->tracking_number }}
			</td>
		</tr>
	</table>

	<table width="100%"x>
		<tr>
			<td colspan="4" rowspan="2">
				Origin: {{ $logistic->routeFrom->company_name }}
			</td>
			<td rowspan="5">
				Destination: 
				@if ($logistic->routeTo->id == 1)
				HQ
				@else
				{{ $logistic->routeTo->company_name }}
				@endif

			</td>
			<td colspan="3">
				Name: {{ $logistic->attention_to }}
			</td>
			<td rowspan="2">
				Control #: <br>
				{{ sprintf('DO%08d', $logistic->id) }}
			</td>
		</tr>
		<tr>
			<td colspan="3">
				Tel No: {{ $logistic->contact_number }}
			</td>
		</tr>
		<tr>
			<td colspan="4" rowspan="3">
				Address: {{ $logistic->routeFrom->address }}
			</td>
			<td colspan="3">
				Email: {{ $logistic->email }}
			</td>
			<td rowspan="3">
				Total Units: <br>
				{{ $logistic->jobs->count() }}
			</td>
		</tr>
		<tr>
			<td colspan="3">
				Address: {{ $logistic->address }}
			</td>
		</tr>
		<tr>
			<td colspan="3">
				Release Date: {{ $logistic->created_at }}
			</td>
		</tr>
		<tr>
			<td>NO.</td>
			<td>J.O #</td>
			<td>MODEL</td>
			<td>IMEI</td>
			<td>COMPLAIN</td>
			<td>REPAIR DONE</td>
			<td>PARTICULARS</td>
			<td>PHYSICAL STATUS</td>
			<td>DATE IN</td>
		</tr>
		@foreach($logistic->jobs as $index => $logjob)
		<tr>
			<td>{{ ++$index }}</td>
			<td>{{ sprintf('JO%08d', $logjob->job_id) }}</td>
			<td>{{ $logjob->job->device->inventory->model->name }}</td>
			<td>{{ $logjob->job->imei }}</td>
			<td>
				@foreach($logjob->job->complaints as $complain)
					@if ($complain == $logjob->job->complaints->last())
					{{ $complain->name }}
					@else
					{{ $complain->name }}, 
					@endif
				@endforeach
			</td>
			<td>
				@if ($logjob->job->special_case == true && $logjob->job->specialCase && $logistic->routeFrom->id == 1)
					{{ $logjob->job->specialCase->new_imei }}
				@else
					@if (!$logjob->job->technicals->isEmpty())
						@foreach($logjob->job->technicals as $technical_info)
							@if( $technical_info->company_id == $logistic->routeFrom->id  )
								<!-- Retrieve from technician remarks (Selected Remarks) -->
								@if ($technical_info->remarks)
									@foreach($technical_info->remarks as $remark)
										@if ($remark == $technical_info->remarks->last())
										{{ $remark->name }}
										@else
										{{ $remark->name }}, 
										@endif
									@endforeach
								@endif

								<!-- Retrieve from technician key-in remark (textfield) -->
								@if ($technical_info->remark)
									@if ($technical_info->remarks)
										, {{$technical_info->remark}}
									@else
										{{$technical_info->remark}}
									@endif
								@endif
							@endif
						@endforeach
					@else
						-
					@endif
				@endif
			</td>
			<td>
				@if( $logjob->job->job_type == 1 )
					{{ $param::getJobType()[$logjob->job->job_type] }}
				@else
					@foreach($logjob->job->accessories as $accessory)
						@if ($accessory == $logjob->job->accessories->last())
							{{ $accessory->name }}
						@else
							{{ $accessory->name }}, 
						@endif
					@endforeach
				@endif
			</td>
			<td>{{ $logjob->job->note }}</td>
			<td>{{ Carbon\Carbon::parse($logjob->job->created_at)->format('d-m-Y') }}</td>
		</tr>
		@endforeach
	</table>

	<br>

	<table width="100%" class="borderless">
		<tr align="center">
			<td width="20%">
				PREPARED BY: <br>
				{{ $logistic->creator->name }} <br> 
				{{ Carbon\Carbon::parse($logistic->created_at)->format('d-m-Y') }} <br>
				(Signature Over Printed Name / Date)
			</td>
			<td>
				QUANTITY CHECKED BY:
				<br><br><br>
				<hr style="margin-bottom: 0em; border-width: 2px;">
				(Signature Over Printed Name / Date)
			</td>
			<td>
				Delivered by:
				<br><br><br>
				<hr style="margin-bottom: 0em; border-width: 2px;">
				(Signature Over Printed Name / Date)
			</td>
			<td>
				Received by:
				<br><br><br>
				<hr style="margin-bottom: 0em; border-width: 2px;">
				(Signature Over Printed Name / Date)
			</td>
		</tr>
	</table>
</div>

@stop