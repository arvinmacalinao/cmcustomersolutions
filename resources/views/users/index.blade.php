@extends('layout')

@section('content')


<div class="content">
	<div class="row">
        <div class="col-md-12">
        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">User Search Panel</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('user.index'),'class'=>'form-horizontal','role'=>'search'])  !!}
					<div class="form-group col-md-6 ">
							{!! Form::label('name', 'Name', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('name', old('name'), array('class' => 'form-control', 'placeholder' => 'Customer Name...')); !!}
						</div>
					</div>
					<!--Add company for search as requested by Alvin Dela Cruz Feb. 18, 2019 c/o Mary Anne Garalde -->
					<div class="form-group col-md-6 ">
							{!! Form::label('company_id', 'Company', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::select('company_id', $companies, old('company_id'), ['class' => 'form-control', 'placeholder' => 'Select a Company...']); !!}
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
				<caption>User List</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>Name</th>
						<th>Email</th>
						<th>Role</th>
						<th>Company</th>
						<th>Status</th>
						<th>Created By</th>
						<th>Created At</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($users as $index => $user)
					<tr>
						<td>{{ (($users->currentPage() - 1 ) * $users->perPage()) + $index + 1 }}</td>
						<td>{{ $user->name }}</td>
						<td>{{ $user->email }}</td>
						<td>{{ $user->role->role_label }}</td>
						<td>{{ $user->company->company_name }}</td>
						<td>
							@if ($user->flag == 1)
							Active
							@else
							Deactivate
							@endif
						</td>
						<td>{{ $user->creator->name }}</td>
						<td>{{ $user->created_at }}</td>
						<td>
							@if ($user->role->role_name != 'super_admin' || $user->id != 2 )
							<a href="{{ route('user.edit', $user->id) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>&nbsp;&nbsp;
							<a href="{{ route('user.password.edit', $user->id) }}"><i class="fa fa-key" aria-hidden="true"></i></a>&nbsp;&nbsp;
								@if ($user->flag == 1)
								<a href="{{ route('user.deactivate', $user->id) }}"><i class="fa fa-times" aria-hidden="true"></i></a>
								@else
								<a href="{{ route('user.activate', $user->id) }}"><i class="fa fa-check" aria-hidden="true"></i></a>
								@endif
							@endif
						</td>	
					</tr>
				    @endforeach
				</tbody>
			</table>
        </div>
    </div>
</div>

{!! $users->appends(request()->query())->render() !!}

@stop