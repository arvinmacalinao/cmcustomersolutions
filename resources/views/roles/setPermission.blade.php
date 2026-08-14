@extends('layout')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Access Control List</div>
                
                <div class="panel-body">
                    {!! Form::open(array( 'route' => array('role.permission.store'), 'method' => 'post', 'class' => 'form-horizontal' )) !!}

					<div class="form-group {{ $errors->has('role_id') ? ' has-error' : '' }}">
					    {!! Form::label('role_id', 'Roles', array('class' => 'col-md-4 control-label')); !!}

					    <div class="col-md-6">
					        {!! Form::select('role_id', $role_list, old('role_id'), ['placeholder' => 'Select a role...', 'class' => 'form-control', 'required']); !!} 

					        @if ($errors->has('role_id'))
					            <span class="help-block">
					            <strong>{{ $errors->first('role_id') }}</strong>
					        </span>
					        @endif
					    </div>
					</div>

					<div class="form-group">
					    <div class="col-md-6 col-md-offset-4">
					        <button type="submit" class="btn btn-primary">
					            <i class="fa fa-btn fa-pencil-square-o"></i> Update
					        </button>
					        <a class="btn btn-danger" href="{{ route('role.permission.create') }}"><i class="fa fa-btn fa-times"></i> Cancel</a>
					    </div>
					</div>

					<div class="col-md-7 col-md-offset-3">
						<ul style="list-style: none;">
							@foreach ($parent_permission as $permission)
								<li>
									{!! Form::checkbox('permission[]', $permission->id, null, ['id' => $permission->id, 'class' => $permission->id]); !!}
							    	<label for="permission[]">{{ $permission->permission_label }}</label>
							    	<ul style="list-style: none;">
							    		@foreach ($child_permission as $subpermission)
								    	@if ($subpermission->parent_id == $permission->id)
									    	<li>
									    		{!! Form::checkbox('permission[]', $subpermission->id, null, ['id' => $permission->id . '-' . $subpermission->id, 'class' => $subpermission->id]); !!}
									    		<label for="permission[]">{{ $subpermission->permission_label }}</label>
									    	</li>
								    	@endif
								    	@endforeach
							    	</ul>
								</li>
							@endforeach
						</ul>
					</div>

					{!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('scripts')
	<script type="text/javascript">

	// Select / deselect all feature for the checkbox.
	$('input[type="checkbox"]').change(function(e) {

		var checked = $(this).prop("checked"),
		container = $(this).parent(),
		siblings = container.siblings();

		container.find('input[type="checkbox"]').prop({
			indeterminate: false,
			checked: checked
		});

		function checkSiblings(el) {

			var parent = el.parent().parent(),
			all = true;

			el.siblings().each(function() {
				return all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
			});

			if (all && checked) {

				parent.children('input[type="checkbox"]').prop({
					indeterminate: false,
					checked: checked
				});

				checkSiblings(parent);

			} else if (all && !checked) {

				parent.children('input[type="checkbox"]').prop("checked", checked);
				parent.children('input[type="checkbox"]').prop("indeterminate", (parent.find('input[type="checkbox"]:checked').length > 0));
				checkSiblings(parent);

			} else {

				el.parents("li").children('input[type="checkbox"]').prop({
					indeterminate: true,
					checked: true // Used to be false
				});

			}

		}

		checkSiblings(container);
    });

	// Retrieve permission_id list for selected role.
    $('select[name="role_id"]').change(function() {
        // Get the value selected (convert spaces to underscores for class selection)
        var roleId = $(this).val();
        //var getPermissionUri = "{!! route('role.permission.show', ['id' => 2]) !!}";
        var getPermissionUri = location.protocol + "//" + location.host + "/role/"+roleId+"/permission";
        
        $.get(getPermissionUri, function(data, status){
            // clear all checkbox
            $(":checkbox").prop("checked",false);
            $.each(data, function (index, value) {
                // Check checkboxes that have class "value"
                $(":checkbox").filter("."+value).prop("checked", true);
            });
        });
    });
    </script>
@stop