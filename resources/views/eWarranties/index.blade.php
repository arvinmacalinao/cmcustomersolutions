@inject('param', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">eWarranty Search Panel</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('ewarranty.index'),'class'=>'form-horizontal','role'=>'search'])  !!}
					<div class="form-group col-md-6 ">
							{!! Form::label('imei', 'IMEI', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('imei', old('imei'), array('class' => 'form-control', 'placeholder' => 'IMEI...')); !!}
						</div>
					</div>

					<div class="form-group col-md-6 ">
							{!! Form::label('model', 'Model', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
			            	{!! Form::text('model', old('model'), array('class' => 'form-control', 'placeholder' => 'Model Code...')); !!}
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
				<caption>eWarranty List</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>IMEI</th>
						<th>Model</th>
						<th>Frontliner Code</th>
						<th>Customer Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Status</th>
						<th>Created Date</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($eWarranties as $index => $eWarranty)
					<tr>
						<td>{{ (($eWarranties->currentPage() - 1 ) * $eWarranties->perPage()) + $index + 1 }}</td>
						<td>{{ $eWarranty->imei }}</td>
						<td>{{ $eWarranty->model }}</td>
						<td>{{ $eWarranty->frontliner_code }}</td>
						<td>{{ $eWarranty->name }}</td>
						<td>{{ $eWarranty->email }}</td>
						<td>{{ $eWarranty->mobile_number }}</td>
						<td>{{ $param::getEWarrantyStatus()[$eWarranty->status] }}</td>
						<td>{{ $eWarranty->created_at }}</td>
					</tr>
				        
				    @endforeach
					
				</tbody>
			</table>

		</div>
	</div>
</div>

{!! $eWarranties->appends(request()->query())->render() !!}

@stop