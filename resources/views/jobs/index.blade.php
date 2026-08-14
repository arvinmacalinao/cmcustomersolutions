@inject('param', 'App\Http\Utilities\GlobalConstant')

@extends('layout')

@section('content')

<div class="content">
	<div class="row">
        <div class="col-md-12">

        	<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Job Search Panel</h3>
				</div>
				<div class="panel-body">
					{!! Form::open(['method'=>'GET','url'=>route('job.index'),'class'=>'form-horizontal','role'=>'search'])  !!}

					<div class="form-group col-md-6 ">
							{!! Form::label('imei', 'IMEI', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
			            	{!! Form::text('imei', old('imei'), array('class' => 'form-control', 'placeholder' => 'Device IMEI...')); !!}
			            </div>
			        </div>
					
					<div class="form-group col-md-6 ">
							{!! Form::label('code', 'Model Code', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('code', old('code'), array('class' => 'form-control', 'placeholder' => 'Model Code...')); !!}
						</div>
					</div>

					<div class="form-group col-md-6 ">
							{!! Form::label('warranty_status', 'Warranty', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::select('warranty_status', $param::getWarrantyStatus(), old('warranty_status'), ['class' => 'form-control', 'placeholder' => 'Device Warranty Status...']); !!}
			            </div>
			        </div>
					
					<div class="form-group col-md-6 ">
							{!! Form::label('job_status_id', 'Job Status', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::select('job_status_id', $job_statuses, old('job_status_id'), ['class' => 'form-control', 'placeholder' => 'Current Job Status...']); !!}
						</div>
					</div>

					<div class="form-group col-md-6 ">
							{!! Form::label('date_from', 'Date From', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
			            	{!! Form::text('date_from', old('date_from'), array('class' => 'form-control', 'placeholder' => 'YYYY-MM-DD')); !!}
			            </div>
			        </div>
					
					<div class="form-group col-md-6 ">
							{!! Form::label('date_to', 'Date To', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('date_to', old('date_to'), array('class' => 'form-control', 'placeholder' => 'YYYY-MM-DD')); !!}
						</div>
					</div>

					<div class="form-group col-md-6 ">
							{!! Form::label('job_level_id', 'Job Level', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::select('job_level_id', $job_levels, old('job_level_id'), ['class' => 'form-control', 'placeholder' => 'Job Level...']); !!}
			            </div>
			        </div>
					
					<div class="form-group col-md-6 ">
							{!! Form::label('limit', 'Record Size', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::select('limit', [50 => 50, 100 => 100, 200 => 200, 500 => 500, 1000 => 1000, 2000 => 2000, 3000 => 3000], old('limit'), ['class' => 'form-control', 'required']); !!}
						</div>
					</div>

					<div class="form-group col-md-6 ">
							{!! Form::label('job_id', 'JO Number', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::text('job_id', old('job_id'), array('class' => 'form-control', 'placeholder' => 'JO Number ie: 218 instead of JO00000218...')); !!}
			            </div>
			        </div>

					@if( Auth::user()->role->role_name == 'super_admin' )
					<div class="form-group col-md-6 ">
							{!! Form::label('company_id', 'Company', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::select('company_id', $companies, old('company_id'), ['class' => 'form-control', 'placeholder' => 'Select a Company...']); !!}
			            </div>
			        </div>
			        @endif
					
					{{--<div class="form-group col-md-6 ">
							{!! Form::label('encode_by', 'Encoder', array('class' => 'control-label col-md-3')); !!}
						<div class="col-md-9">
							{!! Form::select('encode_by', $encoders, old('encode_by'), ['class' => 'form-control', 'placeholder' => 'Select an Encoder...']); !!}
						</div>
					</div>--}}

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
				<caption>Job List</caption>
				<thead>
					<tr>
						<th>#</th>
						<th>Job No.</th>
						<th>Job Type</th>
						<th>IMEI</th>
						<th>Brand</th>
						<th>Model</th>
						<th>Contact Name</th>
						<th>Mobile No.</th>
						<th>Telephone No.</th>
						<th>Created By</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($jobs as $index => $job)
					<tr>
						<td>{{ (($jobs->currentPage() - 1 ) * $jobs->perPage()) + $index + 1 }}</td>
						<td>{{ sprintf('JO%08d', $job->id) }}</td>
						<td>{{ $param::getJobType()[$job->job_type] }}</td>
						<td>{{ $job->imei }}</td>
						<td>{{ $job->device->inventory->model->brand->name }}</td>
						<td>{{ $job->device->inventory->model->code }}</td>
						<td>{{ $job->contact_name }}</td>
						<td>{{ $job->mobile_number }}</td>
						<td>{{ $job->telephone_number }}</td>
						<td>{{ $job->creator->name }}</td>
						<td>{{ $job->status->name }}</td>
						<td>
							<select class = 'form-control job-action'>
								<option selected="selected" value=''>Pick a Task...</option>
								<option value="{{ route('job.form.acknowledgement', $job->id) }}">Print Acknowledge Form</option>
								<option value="{{ route('job.form.joborder', $job->id) }}">Job Order Report</option>
								<option value="{{ route('job.form.techlist', $job->id) }}">Tech Job Report</option>
								<option value="{{ route('job.log', $job->id) }}">View Job Log</option>
								
								@if(Gate::check('reassign_job') &&
									($job->creator->company->id == $job->company_id && 
									$job->company_id != 1 && 
									in_array($job->job_status_id, [3,4,5])) ||
									($job->creator->company->id == $job->company_id && 
									$job->company_id == 1 && 
									in_array($job->job_status_id, [17,18,19])) 
								)
								<option value="{{ route('jobtechnical.cancel', $job->id) }}" id="cancel-tech-job">Cancel Tech Job</option>
								@endif

								@if( Gate::check('cancel_job') && 
									 $job->creator->company->id == $job->company_id && 
									 ($job->job_status_id <= 11 || $job->job_status_id == 29 || $job->job_status_id == 30) &&
									 $job->job_status_id != 32 )
								<option value="{{ route('job.cancel', $job->id) }}" id="cancel-job">Cancel Job</option>
								@endif

								@if( 
									($job->job_status_id == 29 || $job->job_status_id == 30 || $job->job_status_id == 31) &&
									$job->company_id == Auth::user()->company_id
								 )
								<option value="{{ route('job.close', $job->id) }}" id="close-job">Close Job</option>
								@endif
							</select>
						</td>
					</tr>
				    @endforeach
				</tbody>
			</table>

		</div>
	</div>	
</div>

{!! $jobs->appends(request()->query())->render() !!}

@stop

@section('scripts')
<script type="text/javascript">

	// Date feature
    $( function() {
        var dateFrom = $('#date_from').val();
        var dateTo = $('#date_to').val();

        $( "#date_from" ).datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "-5:+0",
            dateFormat: 'yy-mm-dd',
            setDate: dateFrom,
        });

        $( "#date_to" ).datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "-5:+0",
            dateFormat: 'yy-mm-dd',
            setDate: dateTo,
        });
    } );

	$( "select.job-action" ).change(function() {

		if( $(this).children(":selected").attr("id") == 'cancel-job' ) {

			var job_cancellation = prompt("{{trans('validation.confirm_job_cancellation')}}");

			if (job_cancellation === null) {
				$( "select.job-action" ).val("");
				return; //break out of the function early
			}

			//alert( $(this).val() + '?remark=' + encodeURI(job_cancellation) );
			location.href = $(this).val() + '?remark=' + encodeURIComponent(job_cancellation);
		} else if( $(this).children(":selected").attr("id") == 'close-job' ) {
			var job_closure = prompt("{{trans('validation.confirm_job_closure')}}");

			if (job_closure === null) {
				$( "select.job-action" ).val("");
				return; //break out of the function early
			}

			var compiledURL = $(this).val() + '?remark=' + encodeURIComponent(job_closure);

			location.href = compiledURL;
		} else {
			location.href = $(this).val();
		}

		$( "select.job-action" ).val("");
	});

</script>
@stop