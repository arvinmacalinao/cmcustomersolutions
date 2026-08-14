<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Authentication routes...
Route::get('auth/login', ['as' => 'login', 'uses' => 'Auth\AuthController@getLogin']);
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@getLogout']);
	
// Registration routes...
Route::get('auth/register', ['as' => 'register', 'uses' => 'Auth\AuthController@getRegister']);
Route::post('auth/register', 'Auth\AuthController@postRegister');

// Password reset link request routes...
Route::get('password/email', ['as' => 'password.email', 'uses' => 'Auth\PasswordController@getEmail']);
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "auth" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
Route::group(['middleware' => ['auth']], function ()
{
	Route::get('/', ['as' => 'home', 'uses' => 'PagesController@home']);

	// System Migration
	Route::get('import', ['as' => 'import', 'uses' => 'ImportController@index']);
	Route::get('import/register/ewarranty', ['as' => 'import.register.ewarranty', 'uses' => 'EWarrantiesController@checkImportEWarrantyDate']);

	// System Process
	Route::get('device_registration/checkwarranty', ['as' => 'check.warranty', 'uses' => 'DeviceRegistrationController@updateDeviceWarrantyStatus']);

	// eWarranty
	Route::resource('ewarranty', 'EWarrantiesController', ['only' => ['index']]);

	// User Management
	Route::get('profile', ['as' => 'profile', 'uses' => 'UserController@showProfile']);
	Route::patch('profile', ['as' => 'profile.update', 'uses' => 'UserController@updateProfile']);
	Route::get('password', ['as' => 'password.edit', 'uses' => 'UserController@editPassword']);
	Route::patch('password', ['as' => 'password.update', 'uses' => 'UserController@updatePassword']);
	Route::resource('user', 'UserController', ['except' => ['destroy']]);
	Route::get('user/activate/{id}', ['as' => 'user.activate', 'uses' => 'UserController@activateUser']);
	Route::get('user/deactivate/{id}', ['as' => 'user.deactivate', 'uses' => 'UserController@deactivateUser']);
	Route::get('user/password/{id}', ['as' => 'user.password.edit', 'uses' => 'UserController@editPasswordReset']);
	Route::patch('user/password/reset', ['as' => 'user.password.reset', 'uses' => 'UserController@updatePasswordReset']);

	// ACL
	Route::resource('role', 'RoleController', ['except' => ['show']]);
	Route::get('role/create/permission', ['as' => 'role.permission.create', 'uses' => 'RoleController@createRolePermission']);
	Route::post('role/permission', ['as' => 'role.permission.store', 'uses' => 'RoleController@storeRolePermission']);
	Route::get('role/{id}/permission', ['as' => 'role.permission.show', 'uses' => 'RoleController@getRolePermission']);
	Route::resource('permission', 'PermissionController', ['except' => ['show']]);
	
	Route::resource('company', 'CompanyController', ['except' => ['show']]);

	Route::resource('brand', 'BrandController');
	Route::get('model/bom', ['as' => 'model.bom', 'uses' => 'DeviceModelController@getModelBom']);
	Route::get('model/bom/create', ['as' => 'model.bom.create', 'uses' => 'DeviceModelController@createModelBom']);
	Route::post('model/bom/store', ['as' => 'model.bom.store', 'uses' => 'DeviceModelController@storeModelBom']);
	Route::resource('model', 'DeviceModelController');

	Route::resource('complaint', 'ComplaintController', ['except' => ['show']]);
	Route::resource('customer', 'CustomerController', ['except' => ['show']]);
	Route::resource('device_inventory', 'DeviceInventoryController', ['except' => ['show']]);

	Route::get('device_registration/customer', 'DeviceRegistrationController@getCustomer');
	Route::get('device_registration/device', ['as' => 'device_registration.device', 'uses' => 'DeviceRegistrationController@getDevice']);
	Route::resource('device_registration', 'DeviceRegistrationController', ['except' => ['show', 'destroy']]);
	
	Route::get('job/storage', ['as' => 'job.storage', 'uses' => 'JobController@getJobForStorage']);
	Route::get('job/device', ['as' => 'job.device', 'uses' => 'JobController@getDeviceInfo']);
	Route::get('job/api/job/info', ['as' => 'job.api.job.info', 'uses' => 'JobController@getJobInfo']); // Gen Job List for Ticket gen
	Route::get('job/device/img', ['as' => 'job.device.img', 'uses' => 'JobController@getDeviceImg']);
	Route::get('job/model/accessories', ['as' => 'job.model.accessories', 'uses' => 'JobController@getModelAccessories']);
	Route::get('job/cancel/{id}', ['as' => 'job.cancel', 'uses' => 'JobController@cancelJob']);
	Route::get('job/close/{id}', ['as' => 'job.close', 'uses' => 'JobController@closeJob']);
	Route::get('job/log/{id}', ['as' => 'job.log', 'uses' => 'JobController@getJobLog']);

	Route::get('job/form/acknowledgement/{id}', ['as' => 'job.form.acknowledgement', 'uses' => 'JobController@getAcknowledgementForm']);
	Route::get('job/form/joborder/{id}', ['as' => 'job.form.joborder', 'uses' => 'JobController@getJobOrderForm']);
	Route::get('job/form/techlist/{id}', ['as' => 'job.form.techlist', 'uses' => 'JobController@getTechnicalList']);
	
	Route::resource('job', 'JobController', ['except' => ['destroy']]);

	Route::get('special_case/device', ['as' => 'special_case.device', 'uses' => 'SpecialCaseController@getDevice']);
	Route::resource('special_case', 'SpecialCaseController', ['except' => ['create', 'store', 'destroy']]);

	Route::resource('encode_job', 'EncodeJobController', ['except' => ['destroy']]);
	Route::get('encode_job/approve/{id}/{status}', ['as' => 'encode_job.approve', 'uses' => 'EncodeJobController@approveEncodeJob']);

	Route::get('jobtechnical/form/technical/{id}', ['as' => 'jobtechnical.form.technical', 'uses' => 'JobTechnicalController@getTechnicalForm']);
	Route::get('jobtechnical/cancel/{id}', ['as' => 'jobtechnical.cancel', 'uses' => 'JobTechnicalController@cancelTechJob']);
	Route::post('jobtechnical/accept', ['as' => 'jobtechnical.accept', 'uses' => 'JobTechnicalController@acceptJob']);
	Route::resource('jobtechnical', 'JobTechnicalController', ['except' => ['destroy']]);

	Route::post('jobqualitycontrol/accept', ['as' => 'jobqualitycontrol.accept', 'uses' => 'JobQualityControlController@acceptJob']);
	Route::resource('jobqualitycontrol', 'JobQualityControlController', ['except' => ['destroy']]);
	Route::resource('bom', 'BomController', ['except' => ['destroy']]);

	Route::get('warehouse/inventory', ['as' => 'warehouse.inventory', 'uses' => 'WarehouseController@inventory']);
	Route::post('warehouse/store/job', ['as' => 'warehouse.store.job', 'uses' => 'WarehouseController@storeJobItem']);
	Route::resource('warehouse', 'WarehouseController', ['except' => ['show']]);

	Route::get('logistic/{id}/qc', ['as' => 'logistic.qc', 'uses' => 'LogisticController@setLogisticFailJob']);
	Route::get('logistic/{id}/job', ['as' => 'logistic.job', 'uses' => 'LogisticController@getLogisticJob']);
	Route::get('logistic/new/job', 'LogisticController@getDeviceForShipment');
	Route::get('logistic/accept/{id}/{status}', ['as' => 'logistic.accept', 'uses' => 'LogisticController@setShipmentStatus']);
	Route::resource('logistic', 'LogisticController', ['except' => ['destroy']]);
	Route::get('logistic/form/transmittal/hq/{id}', ['as' => 'logistic.form.transmittal.hq', 'uses' => 'LogisticController@getTransmittalHQForm']);

	Route::resource('ticket', 'TicketController', ['except' => ['destroy']]);
	
	Route::get('report/master', ['as' => 'report.list.master', 'uses' => 'ReportController@listMasterReport']);
	Route::get('report/pending', ['as' => 'report.pending', 'uses' => 'ReportController@getPendingReport']);
	Route::get('report/ticket', ['as' => 'report.ticket', 'uses' => 'ReportController@getTicketReport']);
	Route::get('report/{id}', ['as' => 'report.download', 'uses' => 'ReportController@getReport']);
	Route::get('report/gen/dailydrr', ['as' => 'report.gen.master', 'uses' => 'ReportController@genDailyDeviceReceiveReleaseReport']);
	Route::get('report/gen/monthlydrr', ['as' => 'report.gen.master', 'uses' => 'ReportController@genMonthlyDeviceReceiveReleaseReport']);
	Route::get('report/branch/warranty', ['as' => 'report.list.branch.warranty', 'uses' => 'ReportController@getBranchJobWarrantyReport']);
	Route::get('report/branch/total', ['as' => 'report.list.branch.total', 'uses' => 'ReportController@getBranchTotalJobReport']);
	Route::get('report/critical/total', ['as' => 'report.list.critical.total', 'uses' => 'ReportController@getTotalLevelThreeReport']);
	Route::get('report/critical/warranty', ['as' => 'report.list.critical.warranty', 'uses' => 'ReportController@getTotalLevelThreeWarrantyReport']);
	Route::get('report/defect/total', ['as' => 'report.defect.total', 'uses' => 'ReportController@getTotalDefectModelReport']);
	Route::get('report/defect/details', ['as' => 'report.defect.details', 'uses' => 'ReportController@getDetailsDefectModelReport']);
	Route::get('report/csr/performance', ['as' => 'report.csr.performance', 'uses' => 'ReportController@getCSRPerformanceReport']);

	// Testing
	Route::get('report/gen/master', ['as' => 'report.gen.master', 'uses' => 'ReportController@genMasterReport']);
});


/*Event::listen('illuminate.query', function($query)
{
    var_dump($query);
});*/
	
	
	
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| This route group applies the API.
|
 */
Route::group(['prefix' => 'api'], function ()
{
	Route::post('ewarranty', 'EWarrantiesController@api');
	//->withHeaders(['Content-Type' => 'application/json'])
});
