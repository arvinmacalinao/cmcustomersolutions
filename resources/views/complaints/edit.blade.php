@extends('layout')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Update Complaint Details</div>
                    <div class="panel-body">
                        {!! Form::model($complaint, array( 'route' => array('complaint.update', $complaint->id), 
                                                            'method' => 'put', 
                                                            'class' => 'form-horizontal' )) !!}

                        @include('complaints.form')
                        
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-pencil-square-o"></i> Update
                                </button>
                                <a class="btn btn-danger" href="{{ route('complaint.index') }}">
                                    <i class="fa fa-btn fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop