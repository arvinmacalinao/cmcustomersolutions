<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\DeviceRegistrationRequest;
use App\Http\Controllers\Controller;
use App\Http\Utilities\GlobalConstant;

use App\Job;
use App\JobLog;
use App\DeviceRegistration;
use App\DeviceModel;
use App\DeviceInventory;
use App\Customer;
use Auth;
use DB;
use Carbon\Carbon;

class DeviceRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('device_mgmt');

        $limit = $request->input('limit') ? : 50;
        $imei = $request->input('imei') ? : null; 
        $model = $request->input('model') ? : null; 
        
        if( $imei || $model ) {
            $devices = DB::table('device_registrations')
                        ->join('device_inventories', 'device_registrations.imei', '=', 'device_inventories.imei')
                        ->join('device_models', 'device_inventories.device_model_id', '=', 'device_models.id')
                        ->join('users AS creator', 'device_registrations.created_by', '=', 'creator.id')
                        ->join('users AS updater', 'device_registrations.updated_by', '=', 'updater.id')
                        ->join('customers', 'device_registrations.customer_id', '=', 'customers.id')
                        ->select('device_registrations.*', 'customers.name', 'customers.email', 'customers.mobile_number', 'device_models.code', 'creator.name as creator', 'updater.name as updater')
                        //->where('device_registrations.imei', $imei)
                        ->where('device_registrations.flag', true)
                        ->where(function($query) use($imei, $model) {
                                                if ($imei) {
                                                    $query->where('device_registrations.imei', 'like', '%'.$imei.'%');
                                                }
                                                
                                                if ($model) {
                                                    $query->where('device_models.code', 'like', '%'.$model.'%');
                                                }
                                            })
                        ->orderBy('device_registrations.created_at', 'desc')
                        //->tosql();
                        ->paginate($limit);
        } else {
            $devices = DB::table('device_registrations')
                        ->join('device_inventories', 'device_registrations.imei', '=', 'device_inventories.imei')
                        ->join('device_models', 'device_inventories.device_model_id', '=', 'device_models.id')
                        //->join('users', 'device_registrations.created_by', '=', 'users.id')
                        //->join('updaters', 'device_registrations.updated_by', '=', 'users.id')
                        ->join('users AS creator', 'device_registrations.created_by', '=', 'creator.id')
                        ->join('users AS updater', 'device_registrations.updated_by', '=', 'updater.id')
                        ->join('customers', 'device_registrations.customer_id', '=', 'customers.id')
                        ->select('device_registrations.*', 'customers.name', 'customers.email', 'customers.mobile_number', 'device_models.code', 'creator.name as creator', 'updater.name as updater')
                        ->where('device_registrations.flag', true)
                        ->orderBy('device_registrations.created_at', 'desc')
                        ->paginate($limit);
        }
//, 'users.name as creator', 'updaters.name as updater'
        //dd($devices);

        return view('deviceRegistrations.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('device_mgmt');

        return view('deviceRegistrations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\DeviceRegistrationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeviceRegistrationRequest $request)
    {
        $this->authorize('device_mgmt');
        //dd($request);

        // Calculate device warranty date
        $warrantyDuration = DeviceInventory::where('imei', $request->imei)->first()->model->warranty;
        $request['pop_date'] = date('Y-m-d', strtotime($request['pop_date']));
        $request['warranty_date'] = date('Y-m-d', strtotime("+".$warrantyDuration." months", strtotime($request['pop_date'])));

        if( !isset($request['warranty_status']) ) {
            $today = Carbon::now('Asia/Manila')->toDateString();
            $request['warranty_status'] = ($request['warranty_date'] >= $today) ? 1 : 2;
        }
        
        //dd($request->all());
        //DeviceRegistration::create($request->all());
        
        DeviceRegistration::create([
            'imei' => $request['imei'], 
            'customer_id' => $request['customer_id'], 
            'pop_ref' => $request['pop_ref'], 
            'pop_date' => $request['pop_date'], 
            'warranty_date' => $request['warranty_date'], 
            'warranty_status' => $request['warranty_status'], 
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);

        flash(trans('validation.create_success', ['attribute' => 'customer device registration']), 'success');

        return redirect()->route('device_registration.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $imei
     * @return \Illuminate\Http\Response
     */
    public function edit($imei)
    {
        $this->authorize('device_mgmt');

        $device = DeviceRegistration::where('imei', $imei)->lockForUpdate()->firstOrFail();

        //dd($device);

        return view('deviceRegistrations.edit', compact('device'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\DeviceRegistrationRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DeviceRegistrationRequest $request, $imei)
    {
        $this->authorize('device_mgmt');

        DB::transaction(function () use ($request) {
            $request['pop_date'] = date('Y-m-d', strtotime($request['pop_date']));
            $today = Carbon::now('Asia/Manila')->toDateString();

            // set device's warranty status
            if( !isset($request['warranty_status']) ) {
                $request['warranty_status'] = ($request['warranty_date'] >= $today) ? 1 : 2;
            }
            
            // update device's info list
            $device = DeviceRegistration::findOrFail($request['imei']);
            $device_previous_warranty_status = $device->warranty_status;

            $device->update([
                            'customer_id' => $request['customer_id'], 
                            'pop_ref' => $request['pop_ref'], 
                            'pop_date' => $request['pop_date'], 
                            'warranty_date' => $request['warranty_date'], 
                            'warranty_status' => $request['warranty_status'], 
                            'updated_by' => Auth::id()
                            ]);

            // update JO's of device's if exist
            $job_exist = Job::where('imei', $request['imei'])
                                ->where('job_status_id', '<>', 32)
                                ->first();

            if ( $job_exist && $device_previous_warranty_status != $request['warranty_status'] ) {          
                $job_exist->update([
                                        'warranty' => $request['warranty_status'],
                                        'updated_by' => Auth::user()->id
                                    ]);

                /* Log Job */
                $job_log_desc = trans('cdu.job_update_warranty', 
                                                        ['user' => Auth::user()->name, 
                                                        'imei' => $request['imei'],
                                                        'warranty' => GlobalConstant::getWarrantyStatus()[$request['warranty_status']],
                                                        'jobNo' => sprintf('JO%08d', $job_exist->id), 
                                                        'date' => $today]);
                JobLog::setJobLog($job_exist->id, $job_exist->job_status_id, $job_log_desc, $request->user()->id, $request->ip());
            }

            flash(trans('validation.update_success', ['attribute' => 'Update Device Registration']), 'success');
        });

        

        return redirect()->route('device_registration.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    
    }

    /**
     * Retrieve customer list.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCustomer(Request $request)
    {
        /*$this->validate($request, [
            'type' => 'required|in:name,mobile_number,email',
            'search' => 'required',
        ]);*/
        if($request->category == 'name' || $request->category == 'mobile_number'){
            $customers = Customer::where($request->category, 'LIKE', '%'.$request->search.'%')->get();
        }else{
            $customers = Customer::where($request->category, $request->search)->get();
        }

        $html = view('deviceRegistrations.customerList')
                ->with('customers', $customers)
                ->render();
        
        //dd($html);
        
        return $html;
    }

    /**
     * Retrieve device using imei.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDevice(Request $request)
    {
        /*$this->validate($request, [
            'imei' => 'required|regex:/^[0-9]{15,19}$/',
        ]);*/

        $device = DeviceInventory::where('imei', $request->imei)->first();

        $html = view('deviceRegistrations.deviceList')
                ->with('device', $device)
                ->render();

        /*if( $device ){
            $registered = $device->registration ? 'Registered' : 'Not Registered';
            $html = "<tr>
                        <td><input type='radio' name='imei' value='".$device->imei."'</td>
                        <td>".$device->model->model_name."</td>
                        <td>".$device->imei."</td>
                        <td>".$registered."</td>
                    </tr>";
        } else {
            $html = "IMEI not found. You may register the IMEI <a href='".route('device_inventory.create')."'>here</a>";
        }*/
        
        return $html;

        //dd($device);
        //return $device;
    }

    /**
     * Cron Job: Update device .
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDeviceWarrantyStatus()
    {
        $today = Carbon::now('Asia/Manila')->toDateString();
        
        $devices = DeviceRegistration::where('warranty_status', 1)->where('warranty_date', '<', $today)->get();

        foreach ($devices as $key => $device) {
            DeviceRegistration::findOrFail($device->imei)->update([
                'warranty_status' => 2, 
                'updated_by' => 1
            ]);
        }
    }

}
