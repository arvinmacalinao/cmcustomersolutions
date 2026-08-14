<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cherry Mobile</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    
    @yield('styles')

    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body role="document">
    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ route('home') }}">CM</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <!-- .admin dropdown -->
                    @can('administrative')
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Administrative<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            @can('crm')
                            <li class="dropdown-header">Customer Relationship Management</li>
                            <li><a href="{{ route('customer.create') }}">Customer Registration</a></li> 
                            <li><a href="{{ route('customer.index') }}">Customer List</a></li>
                            <li role="separator" class="divider"></li>
                            @endcan
                            @can('user_mgmt')
                            <li class="dropdown-header">User Management</li>
                            <li><a href="{{ route('register') }}">Add User</a></li>
                            <li><a href="{{ route('user.index') }}">User List</a></li>
                            <li role="separator" class="divider"></li>
                            @endcan
                            @can('acl')
                            <li class="dropdown-header">Access Control List</li>
                            <li><a href="{{ route('role.create') }}">Add Role</a></li>
                            <li><a href="{{ route('role.index') }}">Role List</a></li>
                            <li><a href="{{ route('role.permission.create') }}">Assign Permission</a></li>
                            <li role="separator" class="divider"></li>
                            @endcan
                            @can('company_mgmt')
                            <li class="dropdown-header">Company Management</li>
                            <li><a href="{{ route('company.create') }}">Add Company</a></li>
                            <li><a href="{{ route('company.index') }}">Company List</a></li>
                            <li role="separator" class="divider"></li>
                            @endcan
                            @can('complaint_mgmt')
                            <li class="dropdown-header">Complaint Management</li>
                            <li><a href="{{ route('complaint.create') }}">Add Complaint</a></li>
                            <li><a href="{{ route('complaint.index') }}">Complaint List</a></li>
                            <li role="separator" class="divider"></li>
                            @endcan
                            @can('warehouse_mgmt')
                            <li class="dropdown-header">Warehouse Management</li>
                            @can('add_warehouse')
                            <li><a href="{{ route('warehouse.create') }}">Add Warehouse</a></li>
                            @endcan
                            <li><a href="{{ route('warehouse.index') }}">Warehouse List</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcan
                    <!-- /.admin dropdown -->
                    
                    @can('e_warranty')
                    <li><a href="{{ route('ewarranty.index') }}">eWarranty</a></li>
                    @endcan

                    <!-- .job dropdown -->
                    @if( Gate::check('job_mgmt') || Gate::check('workshop') || Gate::check('encode_job') || Gate::check('special_case_mgmt') )
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Job Management<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            @if ( Gate::check('add_job') || Gate::check('edit_job') )
                            <li class="dropdown-header">Job Management</li>
                            @can('add_job')
                            <li><a href="{{ route('job.create') }}">Add Job</a></li>
                            @endcan                            
                            <li><a href="{{ route('job.index') }}">Search Job</a></li>
                            <li role="separator" class="divider"></li>
                            @endif
                            @if ( Gate::check('encode_job') )
                            <li class="dropdown-header">Job Management</li>
                            <li><a href="{{ route('encode_job.index') }}">Encode Job</a></li>
                            <li role="separator" class="divider"></li>
                            @endif

                            @if ( Gate::check('workshop') || Gate::check('job_qc') || Gate::check('special_case_mgmt') )
                            <li class="dropdown-header">Work Shop</li>
                                @if ( Gate::check('workshop') )
                                @if ( Gate::check('super_admin') || Gate::check('branch_admin') || Gate::check('hq_admin') )
                                <li><a href="{{ route('jobtechnical.index') }}">Technical Job Assignment</a></li>
                                @else
                                <li><a href="{{ route('jobtechnical.index') }}">Job List</a></li>
                                @endif
                                @endif

                                @if ( Gate::check('job_qc') )
                                @if ( Gate::check('super_admin') || Gate::check('branch_admin') || Gate::check('hq_admin') )
                                <li><a href="{{ route('jobqualitycontrol.index') }}">Job Quality Control</a></li>
                                @else
                                <li><a href="{{ route('jobqualitycontrol.index') }}">Quality Control List</a></li>
                                @endif
                                @endif

                                @if ( Gate::check('special_case_mgmt') )
                                <li><a href="{{ route('special_case.index') }}">Special Case</a></li>
                                @endif
                            <li role="separator" class="divider"></li>
                            @endif

                            @can('store_inventory')
                            <li class="dropdown-header">Job Storage</li>
                            <li><a href="{{ route('job.storage') }}">Store Device</a></li>
                            <li><a href="{{ route('warehouse.inventory') }}">Inventory</a></li>
                            <li role="separator" class="divider"></li>
                            @endcan
                            
                            @if ( Gate::check('ticket') || Gate::check('ticketing_report') )
                            <li class="dropdown-header">Ticket</li>
                            @can('ticketing_report')
                            <li><a href="{{ route('ticket.create') }}">Create New Ticket</a></li>
                            @endcan
                            <li><a href="{{ route('ticket.index') }}">Ticket List</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endif
                    <!-- /.job dropdown -->

                    <!-- .logistic dropdown -->
                    @if( Gate::check('logistic_mgmt') )
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">    
                            Logistic Management<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-header">Logistic</li>
                            @can('add_delivery_order')
                            <li><a href="{{ route('logistic.create') }}">Create Delivery Order</a></li>
                            @endcan
                            @can('receive_delivery_order')
                            <li><a href="{{ route('logistic.index') }}">Delivery Order List</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endif
                    <!-- /.logistic dropdown -->

                    <!-- .device dropdown -->
                    @can('device_mgmt')
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Device Management<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            @if( Gate::check('manage_brand') || Gate::check('view_brand') )
                            <li class="dropdown-header">Brand Management</li>
                            @can('manage_brand')<li><a href="{{ route('brand.create') }}">Add Brand</a></li>@endcan
                            @can('view_brand')<li><a href="{{ route('brand.index') }}">Brand List</a></li>@endcan
                            <li role="separator" class="divider"></li>
                            @endif
                            @if( Gate::check('manage_model') || Gate::check('view_model') )
                            <li class="dropdown-header">Model Management</li>
                            @can('manage_model')<li><a href="{{ route('model.create') }}">Add Model</a></li>@endcan
                            @can('view_model')<li><a href="{{ route('model.index') }}">Model List</a></li>@endcan
                            <li role="separator" class="divider"></li>
                            @endif
                            @if( Gate::check('manage_inventory') || Gate::check('view_inventory') )
                            <li class="dropdown-header">Inventory Management</li>
                            @can('manage_inventory')<li><a href="{{ route('device_inventory.create') }}">Add Device Inventory</a></li>@endcan
                            @can('view_inventory')<li><a href="{{ route('device_inventory.index') }}">Device Inventory List</a></li>@endcan
                            <li role="separator" class="divider"></li>
                            @endif
                            @can('device_registration_mgmt')
                            <li class="dropdown-header">Customer Device Registration</li>
                            <li><a href="{{ route('device_registration.create') }}">Register Customer Device</a></li>
                            <li><a href="{{ route('device_registration.index') }}">Device Registration List</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcan
                    <!-- /.device dropdown -->

                    <!-- .BOM dropdown -->
                    @can('bom_mgmt')
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">BOM Management<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            @can('bom_item_mgmt')
                            <li><a href="{{ route('bom.index') }}">BOM Item Listing</a></li>
                            <li><a href="{{ route('bom.create') }}">BOM Creation</a></li>
                            @endcan
                            @can('bom_assignment')
                            <li><a href="{{ route('model.bom.create') }}">Model BOM Assignment</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcan
                    <!-- /.BOM dropdown -->

                    <!-- .Report dropdown -->
                    @can('report')
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reports & Analytics<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            @can('master_report')
                            <li><a href="{{ route('report.list.master') }}">Master Report</a></li>
                            @endcan

                            @can('daily_device_receive_release_report')
                            <li>
                                <a href="{{ route('report.list.master', ['type' => 'daily_device_receive_release_report']) }}">
                                    Daily Device Receive / Release Report
                                </a>
                            </li>
                            @endcan
                            
                            @can('monthly_device_receive_release_report')
                            <li>
                                <a href="{{ route('report.list.master', ['type' => 'monthly_device_receive_release_report']) }}">
                                    Monthly Device Receive / Release Report
                                </a>
                            </li>
                            @endcan

                            @if ( Gate::check('branch_job_device_warranty_report') || Gate::check('branch_total_job_report') )
                            <li role="separator" class="divider"></li>
                            <li class="dropdown-header">Branch Report</li>
                            @can('branch_job_device_warranty_report')
                            <li><a href="{{ route('report.list.branch.warranty') }}">Branch Job Warranty Report</a></li>
                            @endcan
                            @can('branch_total_job_report')
                            <li><a href="{{ route('report.list.branch.total') }}">Branch Total Job Report</a></li>
                            @endcan
                            @endif

                            @if ( Gate::check('total_level_three_warranty_type_report') || Gate::check('total_level_three_report') )
                            <li role="separator" class="divider"></li>
                            <li class="dropdown-header">Level 3 Report</li>
                            @can('total_level_three_warranty_type_report')
                            <li><a href="{{ route('report.list.critical.warranty') }}">Total Level 3 Warranty Type Report</a></li>
                            @endcan
                            @can('total_level_three_report')
                            <li><a href="{{ route('report.list.critical.total') }}">Total Level 3 Report</a></li>
                            @endcan
                            @endif

                            @if ( Gate::check('total_model_defect_report') || Gate::check('detailed_model_defect_report') )
                            <li role="separator" class="divider"></li>
                            <li class="dropdown-header">Defect Report</li>
                            @can('total_model_defect_report')
                            <li><a href="{{ route('report.defect.total') }}">Total Model Defect Report</a></li>
                            @endcan
                            @can('detailed_model_defect_report')
                            <li><a href="{{ route('report.defect.details') }}">Detailed Model Defect Report</a></li>
                            @endcan
                            @endif

                            @if ( Gate::check('pending_report') || Gate::check('ticketing_report') || Gate::check('csr_performance_report') )
                            <li role="separator" class="divider"></li>
                            <li class="dropdown-header">Other Report</li>
                            @can('pending_report')
                            <li><a href="{{ route('report.pending') }}">Pending Report</a></li>
                            @endcan
                            @can('ticketing_report')
                            <li><a href="{{ route('report.ticket') }}">Ticketing Report</a></li>
                            @endcan
                            @can('csr_performance_report')
                            <li><a href="{{ route('report.csr.performance') }}">CSR Performance Report</a></li>
                            @endcan
                            @endif
                        </ul>
                    </li>
                    @endcan
                    <!-- /.Report dropdown -->
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user fa-fw"></i><span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="{{ route('profile') }}"><i class="fa fa-user fa-fw"></i> User Profile</a></li>
                            <li><a href="{{ route('password.edit') }}"><i class="fa fa-key fa-fw"></i> Change Password</a></li>
                            <!-- <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a></li> -->
                            <li class="divider"></li>
                            <li><a href="{{ route('logout') }}"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    <!-- /.dropdown -->
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    <div class="container theme-showcase" role="main">
        @include('flash::message')

        @yield('content')
    </div>

    <!-- <hr class="featurette-divider"/>
    <footer class="footer">
        <div class="container">
            <p>&copy; 2016 Cherry Mobile</p>
            <p class="pull-right"><a href="#">Back to top</a></p>
        </div>
    </footer> -->
    
    <!-- 
    The current bootstrap ver. 3.3.6 doesn't support JQuery ver. 3.0 & above. 
    Once bootstrap ver been updated to ver. 3.3.7 or above, remove the the single line jquery code below.
    Go to the gulpfile.js & include the jquery version. -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>  -->

    <!-- Latest compiled and minified JavaScript -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script> -->
    

    <!-- Replace globally hosted script with the one that is hosted locally -->
    <!-- <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
              crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script> -->
    <script src="/js/jquery-2.2.4.min.js"></script>
    <script src="/js/jquery-ui.js"></script>

    <script src="/js/libs.js"></script>

    @yield('scripts')
</body>
</html>
