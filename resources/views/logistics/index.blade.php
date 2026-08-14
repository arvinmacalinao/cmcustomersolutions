@inject('param', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Delivery Order Search Panel</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('logistic.index'),'class'=>'form-horizontal','role'=>'search'])  !!}

					@if( $user_company_id == 1 )
					<div class="form-group col-md-6 ">
							{!! Form::label('company_from', 'Route From', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
			            	{!! Form::select('company_from', $companies, old('company_from'), ['class' => 'form-control', 'placeholder' => 'Select a Company...']); !!}
			            </div>
			        </div>
					
					<div class="form-group col-md-6 ">
							{!! Form::label('company_to', 'Route To', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::select('company_to', $companies, old('company_to'), ['class' => 'form-control', 'placeholder' => 'Select a Company...']); !!}
						</div>
					</div>
					@endif

					<div class="form-group col-md-6 ">
							{!! Form::label('imei', 'IMEI', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('imei', old('imei'), array('class' => 'form-control', 'placeholder' => 'IMEI...')); !!}
			            </div>
			        </div>
					
					<div class="form-group col-md-6 ">
							{!! Form::label('id', 'Tracking No.', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('id', old('id'), array('class' => 'form-control', 'placeholder' => 'ie: 1 instead of DO00000001')); !!}
						</div>
					</div>

					<div class="form-group col-md-12">
			            <div class="col-md-2 col-md-offset-10">
			                <button type="submit" class="btn btn-default-sm">
			                    <i class="fa fa-search" aria-hidden="true"></i> Search
			                </button>
			            </div>
			        </div>
					{!! Form::close() !!}
				</div>
			</div>

			<table class="table">
				<caption>Delivery Order List</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>Route From</th>
						<th>Route To</th>
						<th>Route Type</th>
						<th>Waybill No.</th>
						<th>Attention</th>
						<th>Contact No.</th>
						<th>Status</th>
						<th>Created At</th>
						<th style="width:110px">Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($logistics as $index => $logistic)
					<tr>
						<td>{{ (($logistics->currentPage() - 1 ) * $logistics->perPage()) + $index + 1 }}</td>
						<td>{{ $logistic->routeFrom->company_name }}</td>
						<td>{{ $logistic->routeTo->company_name }}</td>
						@if( $logistic->company_from == $user_company_id )
						<td>Outgoing</td>
						@else
						<td>Incoming</td>
						@endif
						<td>{{ $logistic->waybill_number }}</td>
						<td>{{ $logistic->attention_to }}</td>
						<td>{{ $logistic->contact_number }}</td>
						<td>{{ $param::getLogisticStatus()[$logistic->status] }}</td>
						<td>{{ $logistic->created_at }}</td>
						<td>
							@can('receive_delivery_order')
							<a href="{{ route('logistic.job', $logistic->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
							&nbsp;
							<a href="{{ route('logistic.form.transmittal.hq', $logistic->id) }}"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
							&nbsp;
							@if( $logistic->company_to == $user_company_id && (($user_company_id == 1 && $logistic->status == 1) || ($user_company_id != 1 && $logistic->status == 4)) )
							<a href="{{ route('logistic.accept', [$logistic->id, 1]) }}"><i class="fa fa-check" aria-hidden="true"></i></a>
							&nbsp;
							<a href="{{ route('logistic.accept', [$logistic->id, 0]) }}"><i class="fa fa-times" aria-hidden="true"></i></a>
							@endif
			                @endcan
						</td>
					</tr>
				    @endforeach
				</tbody>
			</table>

		</div>
	</div>	
</div>

{!! $logistics->appends(request()->query())->render() !!}

@stop