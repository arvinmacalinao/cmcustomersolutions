<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\LogisticRequest;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use PDF;
use Carbon\Carbon;

use App\Logistic;
use App\JobLogistic;
use App\JobStorage;
use App\EncodeJob;
use App\Company;
use App\Job;
use App\JobLog;
use App\Warehouse;

class LogisticController extends Controller
{
    /**
     * Display a listing of incoming logistic JO.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('logistic_mgmt');

        $limit = $request->input('limit') ? : 100;
        $status = $request->input('status');
        $company_from = $request->company_from;
        $company_to = $request->company_to;
        $imei = $request->imei;
        $id = $request->id;

        //dd($request->id);

        $user_company_id = Auth::user()->company_id;
        $companies = Company::where('flag', true)->orderBy('company_name', 'ASC')->lists('company_name', 'id');

        //dd(Auth::user()->company_id);

        $logistics = Logistic::with('routeFrom', 'routeTo', 'creator')
                                ->where(function ($query) use($id, $company_from, $company_to, $status) {
                                    $query->where('flag', true);

                                    if ( $id ) {
                                        $query->where('id', $id);
                                    }

                                    if ( $status == 'incoming' ) {
                                        $query->where('status', 1);
                                    }

                                    /*if ( Auth::user()->company_id != 1 ) {
                                        $query->where('company_from', Auth::user()->company_id)
                                                ->orWhere('company_to', Auth::user()->company_id);
                                    }*/

                                    if ( Auth::user()->company_id == 1 && $company_from ) {
                                        $query->where('company_from', $company_from);
                                    }

                                    if ( Auth::user()->company_id == 1 && $company_to ) {
                                        $query->where('company_to', $company_to);
                                    }
                                })
                                ->where(function($query) {
                                    if ( Auth::user()->company_id != 1 ) {
                                        $query->where('company_from', Auth::user()->company_id)
                                                ->orWhere('company_to', Auth::user()->company_id);
                                    }
                                })
                                ->whereHas('jobs.job', function ($query) use($imei) {
                                                if ($imei) {
                                                    $query->where('imei', $imei);
                                                }
                                            })
                                ->orderBy('created_at', 'DESC')
                                //->toSql();
                                ->paginate($limit);
                                
        //dd($logistics);
        //dd($logistics[1]);

        return view('logistics.index', compact('logistics', 'companies', 'user_company_id'));
    }


    /**
     * Show the form for DO creation.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('add_delivery_order');

        $user_company_id = Auth::user()->company_id;

        if( $user_company_id == 1 ) {
            // For HQ staff: Retreive JO ready to be shipped back to Branches
            $companies = Company::where('flag', true)->where('id', '<>', 1)->orderBy('company_name', 'ASC')->lists('company_name', 'id');
            
            $jobs = JobLogistic::where(function ($query) {
                                            $query->where('status', 2);
                                        })
                                ->whereHas('job', 
                                        function ($query) {
                                            //$query->whereIn('job_status_id', [29, 30, 31]);
                                            $query->whereIn('job_status_id', [31, 34]);
                                        })
                                ->get();
        } else {
            // For Branch staff: Retrieve JO ready to be shipped out to HQ
            $jobs = Job::where(function ($query) {
                                $query->where('job_level_id', 3)->where('company_id',  Auth::user()->company_id);
                                $query->whereIn('job_status_id', [1, 6]); // New, Tech Job Complete
                            })->get();
        }

        //dd($jobs);

        return view('logistics.create', compact('companies', 'user_company_id', 'jobs'));
    }


    /**
     * Store a newly created DO.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LogisticRequest $request)
    {
        $this->authorize('add_delivery_order');

        //dd($request->jobs);
        if ( Auth::user()->company_id == 1 ) {
            // For HQ staff: Check whether job qualified to be ship from HQ to Branch.
            $valid_job_status = array(31, 34); // Job Cancelled or Job Ready Shipment

            foreach ($request->jobs as $key => $job_id) {
                    $job_details = Job::find($job_id);

                    if (!$job_details) {
                        flash('Job Order ' . sprintf('JO%08d', $job_id) . ' does not exist.', 'danger');

                        return redirect()->route('logistic.create');
                    }

                    // Check whether job status qualified for shipment
                    if (!in_array($job_details->job_status_id, $valid_job_status)) {
                        // existing code...
                    }
            }

            foreach ($request->jobs as $key => $job_id) {
                    $job_details = Job::find($job_id);

                    // Check whether Job Order exists
                    if (!$job_details) {
                        flash('Job Order ' . sprintf('JO%08d', $job_id) . ' does not exist.', 'danger');

                        return redirect()->route('logistic.create');
                    }

                    // Check whether job status qualified for shipment
                    if (!in_array($job_details->job_status_id, $valid_job_status)) {
                    flash(trans('cdu.err_logistic_job', ['jobNo' => sprintf('JO%08d', $job_id), 
                                                        'company' => $job_details->company->company_name]), 'danger');

                    return redirect()->route('logistic.create');
                }

                // Verify job is redirected to the correct company
                if ($job_details->company_id != $request->get('company_to')) {
                    flash(trans('cdu.job_logistic_fail', ['jobNo' => sprintf('JO%08d', $job_id), 
                                                        'company' => $job_details->company->company_name]), 'danger');

                    return redirect()->route('logistic.create');
                }
            }
        } else {
            // For Branch staff: Check whether job qualified to be ship from Branch to HQ.
            $valid_job_status = array(1, 6); // New, Tech Job Complete

            foreach ($request->jobs as $key => $job_id) {
                // Verify job is redirected to the correct company
                $job_details = Job::find($job_id);

                // Check whether Job Order exists
                if (!$job_details) {
                    flash('Job Order ' . sprintf('JO%08d', $job_id) . ' does not exist.', 'danger');

                    return redirect()->route('logistic.create');
                }

                // Check whether job status qualified for shipment
                if ( !in_array($job_details->job_status_id, $valid_job_status) ) {
                    flash(trans('cdu.err_logistic_job', ['jobNo' => sprintf('JO%08d', $job_id), 
                                                        'company' => $job_details->company->company_name]), 'danger');

                    return redirect()->route('logistic.create');
                }
            }
        }

        DB::transaction(function () use ($request) {
            $company_from = Auth::user()->company->company_name;
            $today = Carbon::now('Asia/Manila');

            //dd($logistic->id);
            if ( Auth::user()->company_id == 1 ) {
                // Create DO to ship device from HQ back to Branch
                $this->validate($request, [
                                            'company_to' => 'required|integer|exists:companies,id',
                                        ]);

                $company_to = DB::table('companies')->where('id', $request->get('company_to'))->value('company_name');
                $job_status_id = 14; // status for 'Route to Branch'

                $logistic = Logistic::create([
                                            'company_from' => Auth::user()->company_id,
                                            'company_to' => $request->get('company_to'),
                                            'waybill_number' => $request->waybill_number,
                                            'attention_to' => $request->get('attention_to'),
                                            'email' => $request->get('email'),
                                            'contact_number' => $request->get('contact_number'),
                                            'remark' => $request->get('remark'),
                                            'address' => $request->get('address'),
                                            'postcode' => $request->get('postcode'),
                                            'state_id' => $request->get('state_id'),
                                            'status' => 4,
                                            'flag' => true,
                                            'created_by' => Auth::id()
                                            ]);
                
                foreach ($request->jobs as $key => $job_id) {
                    // Create individual selected JO for logistic transfer back to Branch 
                    JobLogistic::where(function ($query) use($job_id, $logistic) {
                                            $query->where('status', 1);
                                            $query->where('job_id', $job_id);
                                        })
                                ->whereHas('job', 
                                        function ($query) {
                                            //$query->whereIn('job_status_id', [29, 30, 31, 34]);
                                            $query->whereIn('job_status_id', [31, 34]);
                                        })
                                ->update(['status' => 4]);

                    JobLogistic::create([
                                        'job_id' => $job_id,
                                        'logistic_id' => $logistic->id,
                                        'encode_by' => Auth::id(),
                                        'status' => 4,
                                        'flag' => true,
                                        'created_by' => Auth::id()
                                        ]);

                    Job::where('id', $job_id)->update(['job_status_id' => $job_status_id]);

                    // Update JO's device warehouse storage status
                    $warehouses = Warehouse::where('flag', true)->where('company_id', Auth::user()->company_id)->lists('id')->toArray();
                    
                    JobStorage::where('job_id', $job_id)
                                ->where('status', true)
                                ->whereIn('warehouse_id', $warehouses)
                                ->update([
                                            'status' => false,
                                            'updated_by' => Auth::id()
                                        ]);

                    // Log Job 
                    $job_log_desc = trans('cdu.route_job', ['jobNo' => sprintf('JO%08d', $job_id), 
                                                            'from' => $company_from, 
                                                            'to' => $company_to, 
                                                            'user' => Auth::user()->name, 
                                                            'date' => $today]);
                    JobLog::setJobLog($job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());
                }
            } else {
                // Create DO to ship device from Branch to HQ
                $company_to = DB::table('companies')->where('id', 1)->value('company_name');
                $job_status_id = 12; // status for 'Route to HQ'

                $logistic = Logistic::create([
                                            'company_from' => Auth::user()->company_id,
                                            'company_to' => 1,
                                            'waybill_number' => $request->waybill_number,
                                            'attention_to' => $request->get('attention_to'),
                                            'email' => $request->get('email'),
                                            'contact_number' => $request->get('contact_number'),
                                            'remark' => $request->get('remark'),
                                            'address' => $request->get('address'),
                                            'postcode' => $request->get('postcode'),
                                            'state_id' => $request->get('state_id'),
                                            'status' => 1,
                                            'flag' => true,
                                            'created_by' => Auth::id()
                                            ]);
                
                foreach ($request->jobs as $key => $job_id) {
                    // Create individual selected JO for logistic transfer to HQ
                    JobLogistic::create([
                                        'job_id' => $job_id,
                                        'logistic_id' => $logistic->id,
                                        'encode_by' => Auth::id(),
                                        'status' => 1,
                                        'flag' => true,
                                        'created_by' => Auth::id()
                                        ]);

                    DB::table('jobs')->where('id', $job_id)
                                    ->update(['job_status_id' => $job_status_id]);

                    // Log Job
                    $job_log_desc = trans('cdu.route_job', ['jobNo' => sprintf('JO%08d', $job_id), 
                                                            'from' => $company_from, 
                                                            'to' => $company_to, 
                                                            'user' => Auth::user()->name, 
                                                            'date' => $today]);
                    JobLog::setJobLog($job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());
                }
            }
        }, 5);
        
        flash(trans('validation.create_success', ['attribute' => 'delivery order']), 'success');

        return redirect()->route('logistic.index');
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
     * Get device awaiting shipment.
     *
     * @param  Request $request
     * @return Html
     */
    public function getDeviceForShipment(Request $request)
    {
        $this->authorize('logistic_mgmt');

        $user_company_id = $request->user_company_id;
        $ship_route_id = $request->ship_route_id;

        $jobs = Job::where(function ($query) use($user_company_id, $ship_route_id) {
                                $query->where('job_level_id', 3);

                                if ($user_company_id == 1) {
                                    // Ship device back to Branch
                                    $query->where('company_id', $ship_route_id);
                                    //$query->whereIn('job_status_id', [22, 29, 30]); // HQ QC Approve, Complete, Complete on time
                                    $query->whereIn('job_status_id', [31, 34]); // Job Cancelled or Job Ready Shipment
                                    
                                } else {
                                    // Ship to HQ
                                    $query->whereIn('job_status_id', [1, 6]); // New, Tech Job Complete
                                }
                            })
                    ->get();

        $html = view('logistics.jobList')
                ->with('jobs', $jobs)
                ->render();
        
        return $html;
    }

    /**
     * Get list of JO assign to shipment.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function getLogisticJob($id)
    {
        $this->authorize('logistic_mgmt');

        $logistic = Logistic::find($id);

        //dd($logistic->jobs[0]->job->device->inventory->model);

        return view('logistics.job', compact('logistic'));
    }
    

    /**
     * Accept incoming shipment.
     *
     * @param  int $id
     * @param  int $status
     * @return Collection
     */
    public function setShipmentStatus($id, $status)
    {
        $this->authorize('receive_delivery_order');
        
        //dd(Auth::user()->company_id);
        DB::transaction(function () use ($id, $status) {
            $today = Carbon::now('Asia/Manila');

            if( $status == 1 ) {
                // Accept incoming logistic
                if ( Auth::user()->company_id == 1 ) {
                    // HQ Admin
                    $logistic_status = 2;
                    $job_logistic_status = 2;
                    $job_status_id = 13; // HQ accepted incoming job shipped from branch;
                } else {
                    // Branch Admin
                    $logistic_status = 5;
                    $job_logistic_status = 5;
                    $job_status_id = 15; // Branch accepted completed job shipped from HQ;
                }
            } else {
                // Reject incoming logistic
                if ( Auth::user()->company_id == 1 ) {
                    // HQ Admin
                    $logistic_status = 3;
                    $job_logistic_status = 3;
                } else {
                    // Branch Admin
                    $logistic_status = 6;
                    $job_logistic_status = 6;
                }
            }
            
            // Logistic accept or deny DO
            $update_logistic = Logistic::where(function ($query) use($id) {
                                            $query->where('id', $id);
                                            if ( Auth::user()->company_id == 1 ) {
                                                $query->where('status', 1);
                                            } else {
                                                $query->where('status', 4);
                                            }
                                            $query->where('flag', true);
                                            $query->where('company_to', Auth::user()->company_id);
                                        })
                                        ->update(['status' => $logistic_status]);

            if( $update_logistic ) {
                $job_logistics = JobLogistic::where('logistic_id', $id)->get();
                
                foreach ($job_logistics as $key => $job_logistic) {
                    // Update job logistic's status
                    $job_logistic->update(['status' => $job_logistic_status]);

                    if ( $status == 1 ) {
                        // Accept incoming device
                        if ( !EncodeJob::where('status', null)->where('job_logistic_id', $job_logistic->id)->exists() && Auth::user()->company_id == 1 ) {
                            // Create individual device for physical encoder
                            EncodeJob::create([ 'job_logistic_id' => $job_logistic->id, 
                                                //'description' => 'Ready to be encode', 
                                                'created_by' => Auth::user()->id, 
                                                'updated_by' => Auth::user()->id]);
                        }
                        
                        Job::where('id', $job_logistic->job_id)->update(['job_status_id' => $job_status_id]);

                        /* Log Job */
                        $job_log_desc = trans('cdu.accept_job_route', ['jobNo' => sprintf('JO%08d', $job_logistic->job_id),
                                                                        'to' => $job_logistic->logistic->routeTo->company_name, 
                                                                        'user' => Auth::user()->name, 
                                                                        'date' => $today]);
                        JobLog::setJobLog($job_logistic->job_id, $job_status_id, $job_log_desc, Auth::user()->id, request()->ip());
                    } else {
                        // Reject incoming device
                        //TODO: need to re-store device into warehouse?

                        if( Auth::user()->company_id == 1 ) {
                            // HQ reject incoming devices
                            $job_status_id = 1; // set job status to new;
                        } else {
                            // Branch reject incoming devices
                            // if not set job status as cancelled (31), else, Job Shipment Ready (34)
                            $warehouses = Warehouse::where('flag', true)
                                                    ->where('company_id', 1)
                                                    ->lists('id')
                                                    ->toArray(); // Retrieve HQ's warehouse

                            $stored_device = JobStorage::where('job_id', $job_logistic->job_id)
                                                        ->where('status', false)
                                                        ->whereIn('warehouse_id', $warehouses)
                                                        ->get();
                                                        //dd($stored_device);
                            if( $stored_device ) {
                                $job_status_id = 34;

                                // restore device's warehouse storage status
                                JobStorage::where('job_id', $job_logistic->job_id)
                                            ->where('status', false)
                                            ->whereIn('warehouse_id', $warehouses)
                                            ->update([
                                                        'status' => true,
                                                        'updated_by' => Auth::id()
                                                    ]);
                            } else {
                                $job_status_id = 31;
                            }
                        }

                        Job::where('id', $job_logistic->job_id)->update(['job_status_id' => $job_status_id]);

                        // Log Job
                        $job_log_desc = trans('cdu.reject_job_route', ['jobNo' => sprintf('JO%08d', $job_logistic->job_id),
                                                                        'to' => $job_logistic->logistic->routeTo->company_name, 
                                                                        'user' => Auth::user()->name, 
                                                                        'date' => $today]);
                        JobLog::setJobLog($job_logistic->job_id, $job_status_id, $job_log_desc, Auth::user()->id, request()->ip());
                    }
                }
            }
        }, 5);

        flash(trans('validation.update_success', ['attribute' => 'delivery order status']), 'success');

        return redirect()->route('logistic.index');
    }


    /**
     * Generate Transmittal Form.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTransmittalHQForm(Request $request)
    {
        $this->authorize('logistic_mgmt');

        $logistic = Logistic::with('routeFrom', 'routeTo', 'jobs', 'creator')
                                ->where('flag', true)
                                ->where('id', $request->id)
                                ->firstOrFail();
        //dd($logistic->jobs[0]->job->technicals->last()->remarks[0]->name);
        //dd($logistic->jobs[0]->job->technicals->last()->remark);
        //dd($logistic);

        $pdf = PDF::loadView('forms.transmittalHQ', compact('logistic'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream();
    }

    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setLogisticFailJob($id)
    {
        $this->authorize('logistic_mgmt');

        $today = Carbon::now('Asia/Manila');

        $status = 'fail';
        $job_status_id = 24;

        // Update Job status
        Job::find($id)->update([
                                    'job_status_id' => $job_status_id,
                                    'updated_by' => Auth::user()->id
                                ]);

        // Update Job Storage Status
        $warehouses = Warehouse::where('flag', true)->where('company_id', Auth::user()->company_id)->lists('id')->toArray();

        $test = JobStorage::where('job_id', $id)
                            ->where('status', true)
                            ->whereIn('warehouse_id', $warehouses)
                            ->update([
                                    'status' => false,
                                    'updated_by' => Auth::id()
                                ]);
                    //->delete();

        /* Log Job */
        $job_log_desc = trans('cdu.complete_job_qc', 
                                                ['user' => Auth::user()->name, 
                                                'jobNo' => sprintf('JO%08d', $id), 
                                                'status' => $status, 
                                                'date' => $today]);

        JobLog::setJobLog($id, $job_status_id, $job_log_desc, Auth::user()->id, $_SERVER['REMOTE_ADDR']);

        return redirect()->back();
    }
}
