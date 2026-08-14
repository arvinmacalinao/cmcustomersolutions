<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\WarehouseRequest;
use App\Http\Controllers\Controller;

use Auth;
use Carbon\Carbon;

use App\Warehouse;
use App\Job;
use App\JobLog;
use App\JobStorage;
use App\Company;
use App\State;

class WarehouseController extends Controller
{
    /**
     * Display a listing of warehouses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('warehouse_mgmt');

        $limit = $request->input('limit') ? : 100;
        $warehouse_name = $request->warehouse_name;
        $company_id = $request->company_id;

        $companies = Company::where('flag', true)->lists('company_name', 'id');

        $warehouses = Warehouse::with('company', 'state', 'creator')
                                ->where(function ($query) use($warehouse_name, $company_id) {
                                    $query->where('flag', true);

                                    if ($warehouse_name) {
                                        $query->where('name', $warehouse_name);
                                    }

                                    if ($company_id) {
                                        $query->where('company_id', $company_id);
                                    }
                                })
                                ->paginate($limit);

        return view('warehouses.index', compact('warehouses', 'companies'));
    }


    /**
     * Show the form for creating a new warehouse.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add_warehouse');

        $company_list = Company::where('flag', true)->lists('company_name', 'id');
        $state_list = State::where('flag', true)->lists('state_name', 'id');
        
        return view('warehouses.create', compact('company_list', 'state_list'));
    }


    /**
     * Store a newly created warehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WarehouseRequest $request)
    {
        $this->authorize('add_warehouse');

        Warehouse::create([
                        'name' => $request->name, 
                        'company_id' => $request->company_id, 
                        'address' => $request->address, 
                        'postcode' => $request->postcode, 
                        'state_id' => $request->state_id, 
                        'status' => 1, 
                        'flag' => true,
                        'created_by' => $request->user()->id,
                        ]);

        flash(trans('validation.create_success', ['attribute' => 'warehouse']), 'success');

        return redirect()->route('warehouse.index');
    }


    /**
     * Show the form for editing warehouse.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('edit_warehouse');

        $warehouse = Warehouse::find($id);
        $company_list = Company::where('flag', true)->lists('company_name', 'id');
        $state_list = State::where('flag', true)->lists('state_name', 'id');
        
        return view('warehouses.edit', compact('warehouse', 'company_list', 'state_list'));
    }


    /**
     * Update the specified warehouse info.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(WarehouseRequest $request, $id)
    {
        $this->authorize('edit_warehouse');

        Warehouse::find($id)->update([
                                    'name' => $request->name, 
                                    'company_id' => $request->company_id, 
                                    'address' => $request->address, 
                                    'postcode' => $request->postcode, 
                                    'state_id' => $request->state_id, 
                                    'status' => 1, 
                                    'flag' => true,
                                    'updated_by' => $request->user()->id,
                                    ]);

        flash(trans('validation.update_success', ['attribute' => 'warehouse']), 'success');

        return redirect()->route('warehouse.index');
    }


    /**
     * Remove the specified warehouse from DB.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete_warehouse');
        
        Warehouse::find($id)->update([
                                    'status' => 2,
                                    'flag' => false,
                                    'updated_by' => Auth::id()
                                    ]);

        flash(trans('validation.delete_success', ['attribute' => 'warehouse']), 'success');

        return redirect()->route('warehouse.index');
    }


    /**
     * Store completed Job device in warehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeJobItem(Request $request)
    {
        $this->authorize('store_inventory');
        $today = Carbon::now();

        // TODO: Move function to job storage controller under store method
        foreach ($request->job_id as $key => $job_id) {
            JobStorage::create([
                                'job_id' => $job_id, 
                                'warehouse_id' => $request->warehouse_id, 
                                'status' => 1,
                                'created_by' => $request->user()->id,
                                ]);

            $job = Job::find($job_id);
            $warehouse = Warehouse::find($request->warehouse_id);

            // Update Job status
            if ( Auth::user()->company_id == 1 && $job->company_id != 1 ) {
                $job_status_id = 34;
            } else {
                if( $job->expire_date >= $today->toDateString() ) {
                    $job_status_id = 29; // job complete on time
                } else {
                    $job_status_id = 30; // job complete but overdue
                }
            }

            $job->update([
                            'job_status_id' => $job_status_id,
                            'updated_by' => $request->user()->id
                        ]);

            // Log Job 
            $job_log_desc = trans('cdu.store_job_device', [ 'jobNo' => sprintf('JO%08d', $job_id), 
                                                            'warehouse' => $warehouse->name, 
                                                            'user' => $request->user()->name, 
                                                            'date' => $today ]);
            JobLog::setJobLog($job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());
        }

        flash(trans('cdu.store_job_device_success', ['warehouse' => $warehouse->name]), 'success');
        
        return redirect()->route('job.storage');
    }


    /**
     * Get Jobs' device stored in warehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inventory(Request $request)
    {
        $this->authorize('store_inventory');
        
        // TODO: Move function to job storage controller under index method
        $limit = $request->input('limit') ? : 100;
        $imei = trim($request->input('imei'));
        $code = trim($request->input('code'));

        $this->authorize('store_inventory');

        $inventories = JobStorage::where('status', true)
                                    ->whereHas('warehouse', function ($query) {
                                                $query->where('company_id', Auth::user()->company_id);
                                            })
                                    ->whereHas('job', function ($query) use($imei) {
                                                if( $imei ) {
                                                        $query->where('imei', 'like', '%'.$imei.'%');
                                                    }
                                            })
                                    ->whereHas('job.device.inventory.model', function ($query) use($code) {
                                                if ($code) {
                                                    $query->where('code', 'like', '%'.$code.'%');
                                                }
                                            })
                                    ->paginate($limit);

        //dd($inventories[0]->warehouse);

        return view('warehouses.inventory', compact('inventories'));
    }
}
