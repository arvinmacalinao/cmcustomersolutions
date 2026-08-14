<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use DB;
use Carbon\Carbon;

use App\SpecialCase;
use App\DeviceInventory;
use App\JobLog;
use App\DeviceRegistration;

class SpecialCaseController extends Controller
{
    /**
     * Display a listing of special cases.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('special_case_mgmt');

        $limit = $request->input('limit') ? : 100;
        $status = $request->input('status');

        $special_cases = SpecialCase::with('creator')
                            ->where(function ($query) use($status) {
                                $today = Carbon::now('Asia/Manila');

                                if ( $status == 'new' ) {
                                    $query->where('status', 1);
                                }

                                if ( $status == 'expire' ) {
                                    $query->where('status', 1);
                                    $query->where('created_at', '<', $today->subDays(8)->toDateString());
                                }
                                
                            })
                            ->orderBy('id', 'desc')
                            ->paginate($limit);

        return view('specialCases.index', compact('special_cases'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 
    }

    /**
     * Store a newly created resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display a specified special case.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing special case.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('approve_special_case');

        $case = SpecialCase::find($id);

        return view('specialCases.edit', compact('case'));
    }


    /**
     * Update the specified special case.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('approve_special_case');

        // Check whether IMEI exist in inventory, matches model & has not yet been registered
        // Update special_case, job log & job status to 20
        DB::transaction(function () use ($request, $id) {
            $today = Carbon::now('Asia/Manila');
            $special_case = SpecialCase::find($id);

            if( $request->pass_btn ) {
                // Pass special case approval
                $job_status_id = 23; // job_complete
                $special_case_status = 2;
                $status = 'Approved';

                $customer = $special_case->serviceDevice->registration;
                // Register newly assigned device to customer
                DeviceRegistration::create([
                                            'imei' => $request->imei, 
                                            'customer_id' => $customer->customer_id, 
                                            'pop_ref' => $customer->pop_ref, 
                                            'pop_date' => $customer->pop_date, 
                                            'warranty_date' => $customer->warranty_date, 
                                            'warranty_status' => $customer->warranty_status, 
                                            'created_by' => $request->user()->id,
                                            'updated_by' => $request->user()->id,
                                        ]);

                /* Log Job: Customer device registration */
                $job_log_desc = trans('cdu.complete_cust_device_reg', ['imei' => $request->imei,
                                                                        'date' => $today]);
                JobLog::setJobLog($special_case->job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());

                // Deactivate customer's previous device & device inventory
                DeviceRegistration::where('imei', $request->old_imei)
                                    ->update(['flag' => 0]);
                DeviceInventory::where('imei', $request->old_imei)
                                    ->update(['flag' => 0]);
            } else {
                $job_status_id = 16; // physical_encoded
                $special_case_status = 3;
                $status = 'Denied';
            }
                
            $special_case->update(['new_imei' => $request->imei, 
                                    'comment' => $request->comment, 
                                    'status' => $special_case_status, 
                                    'updated_by' => $request->user()->id
                                ]);
            
            $status_update = $special_case->job->update(['job_status_id' => $job_status_id]);

            /* Log Job */
            $job_log_desc = trans('cdu.complete_special_case', ['jobNo' => sprintf('JO%08d', $special_case->job_id), 
                                                                'status' => $status, 
                                                                'user' => Auth::user()->name, 
                                                                'date' => $today]);
            JobLog::setJobLog($special_case->job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());           
        }, 5);

        flash(trans('validation.update_success', ['attribute' => 'special case']), 'success');

        return redirect()->route('special_case.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Retrieve device replacement based on IMEI.
     *
     * @param  string  $imei
     * @return html DeviceInventory
     */
    public function getDevice(Request $request)
    {
        $device = DeviceInventory::where('imei', $request->imei)->first();
        
        if( empty($device) || !empty($device->registration) ) {
            $device['error'] = 'IMEI ('.$request->imei.') for device is not available.' ;
        }
        
        $html = view('specialCases.deviceList')
                ->with('device', $device)
                ->render();

        return $html;
    }
}
