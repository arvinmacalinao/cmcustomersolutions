@inject('globalVar', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">BOM Search Panel</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('bom.index'),'class'=>'form-horizontal','role'=>'search'])  !!}
					
					<div class="form-group col-md-6 ">
							{!! Form::label('code', 'BOM Code', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('code', old('code'), array('class' => 'form-control', 'placeholder' => 'BOM Code...')); !!}
						</div>
					</div>

					<div class="form-group col-md-6 ">
							{!! Form::label('name', 'BOM Name', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
			            	{!! Form::text('name', old('name'), array('class' => 'form-control', 'placeholder' => 'BOM Name...')); !!}
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
				<caption>BOM List</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>Code</th>
						<th>Name</th>
						<th>Brand</th>
						<th>Warranty</th>
						<th>Quantity</th>
						<th>SRP</th>
						<th>Retail Price</th>
						<th>Dealer Price</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($bom as $index => $item)
					<tr>
						<td>{{ (($bom->currentPage() - 1 ) * $bom->perPage()) + $index + 1 }}</td>
						<td>{{ $item->code }}</td>
						<td>{{ $item->name }}</td>
						<td>{{ $item->brand->name }}</td>
						<td>{{ $item->warranty }}</td>
						<td>{{ $item->quantity }}</td>
						<td>{{ $item->suggested_retail_price }}</td>
						<td>{{ $item->retail_price }}</td>
						<td>{{ $item->dealer_price }}</td>
						<td>{{ $globalVar::getBOMStatus()[$item->status] }}</td>
						<td><a href="{{ route('bom.edit', $item->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></td>
					</tr>
				    @endforeach
				</tbody>
			</table>

		</div>
	</div>	
</div>

{!! $bom->appends(request()->query())->render() !!}

@stop