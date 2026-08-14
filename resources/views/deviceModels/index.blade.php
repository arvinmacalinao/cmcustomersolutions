@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Model Search Panel</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('model.index'),'class'=>'form-horizontal','role'=>'search'])  !!}
					<div class="form-group col-md-6 ">
							{!! Form::label('code', 'Model Code', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('code', old('code'), array('class' => 'form-control', 'placeholder' => 'Model Code...')); !!}
						</div>
					</div>

					<div class="form-group col-md-6 ">
							{!! Form::label('name', 'Model Name', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
			            	{!! Form::text('name', old('name'), array('class' => 'form-control', 'placeholder' => 'Model Name...')); !!}
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
				<caption>Model List</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>Code</th>
						<th>Name</th>
						<th>Brand</th>
						<th>Type</th>
						<th>Warranty (Months)</th>
						<th>Price</th>
						<th>Labor Cost 1</th>
						<th>Labor Cost 2</th>
						<th>Labor Cost 3</th>
						<th>Created By</th>
						<th>Date Created</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($models as $index => $model)
					<tr>
						<td>{{ (($models->currentPage() - 1 ) * $models->perPage()) + $index + 1 }}</td>
						<td>{{ $model->code }}</td>
						<td>{{ $model->name }}</td>
						<td>{{ $model->brand->name }}</td>
						<td>{{ $model->deviceType->name }}</td>
						<td>{{ $model->warranty }}</td>
						<td>{{ $model->price }}</td>
						<td>{{ $model->labor_cost_1 }}</td>
						<td>{{ $model->labor_cost_2 }}</td>
						<td>{{ $model->labor_cost_3 }}</td>
						<td>{{ $model->creator->name }}</td>
						<td>{{ $model->created_at }}</td>
						<td>
							<a href="{{ route('model.edit', $model->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
							&nbsp;&nbsp;
							@can('manage_model')
							{!! Form::model($model, array('method' => 'delete', 'route' => ['model.destroy', $model->id], 'id' => 'delete_model_'.$model->id, 'style' => 'display:inline')) !!}
							<i id="{{$model->id}}" class="fa fa-times"></i>
			                {!! Form::close() !!}
			                @endcan
						</td>
					</tr>
				        
				    @endforeach
					
				</tbody>
			</table>

		</div>
	</div>	
</div>

{!! $models->appends(request()->query())->render() !!}

@stop


@section('scripts')
<script type="text/javascript">
	$( "i.fa.fa-times" ).click(function() {
		var record_removal = confirm("{{trans('validation.confirm_delete', ['attribute' => 'device model'])}}");

		if (record_removal == true) {
			document.getElementById('delete_model_'+this.id).submit();
		} else {
			return false;
		}
	});
</script>
@stop