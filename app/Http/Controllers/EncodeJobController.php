<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use DB;
use Carbon\Carbon;

use App\EncodeJob;
use App\JobLevel;
use App\Company;
use App\Job;
use App\JobLog;
use App\SpecialCase;

class EncodeJobController extends Controller
{
    /**
     * Display a listing Job ready for encoding.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('encode_job');

        $limit = $request->input('limit') ? : 100;
        $imei = trim($request->input('imei'));
        $code = trim($request->input('code'));
        $job_encode_status = trim($request->input('job_encode_status'));

        // Retrieve value to populate dropdown field
        $job_levels = JobLevel::where('flag', true)->lists('name', 'id');
        $companies = Company::where('flag', true)->lists('company_name', 'id');


        // TODO: Only HQ would need to encode job
        if (Auth::user()->company_id == 1) {
            $encode_jobs = EncodeJob::with('creator')
                                    ->where(function ($query) {
                                        if( Auth::user()->role_id != 2 ) {
                                            $query->whereNull('status');
                                        }
                                    })
                                    ->whereHas('jobLogistic.logistic', function ($query) {
                                                            $company_id = 1; // HQ
                                                            $query->where('company_to', '=', $company_id);
                                                        })
                                    ->whereHas('jobLogistic.job', function ($query) use($imei) {
                                                            if ($imei) {
                                                                $query->where('imei', 'like', '%'.$imei.'%');
                                                            }
                                                        })
                                    ->whereHas('jobLogistic.job.device.inventory.model', function ($query) use($code) {
                                                            if ($code) {
                                                                $query->where('code', 'like', '%'.$code.'%');
                                                            }
                                                        })
                                    ->orderBy('id', 'desc')
                                    ->paginate($limit);
        } else {
            $encode_jobs = collect([]);
        } 

        return view('encodeJobs.index', compact('encode_jobs', 'companies'));
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the encode job for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('encode_job');

        $encode_job = EncodeJob::find($id);
        $job_details = $encode_job->jobLogistic->job;

        //dd($encode_job->jobLogistic->job->image);

        return view('encodeJobs.edit', compact('encode_job', 'job_details'));
    }


    /**
     * Accept / Deny Job Encoding.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('encode_job');

        //dd($request->all());

        DB::transaction(function () use ($request, $id) {
            $today = Carbon::now('Asia/Manila');

            if( $request->pass_btn ) {
                if( Auth::user()->company_id == 1 ) {
                    $encode_job_status = 2; // Accepted by Encoder
                } else {
                    $encode_job_status = 6; // Accepted by Branch Admin
                }
            } elseif ( $request->fail_btn ) {
                if( Auth::user()->company_id == 1 ) {
                    $encode_job_status = 3; // Rejected by Encoder
                } else {
                    $encode_job_status = 6; // Accepted by Branch Admin
                }
            } 

            // Accept of deny encode job
            $logistic = EncodeJob::where(function ($query) use($id) {
                                        $query->where('id', $id);
                                        $query->where('flag', true);
                                    })
                                    ->update(['status' => $encode_job_status, 
                                            'description' => $request->description]);

            $job = EncodeJob::find($id)->jobLogistic->job;
            $job_id = $job->id;
            $special_case = $job->special_case;
            //$job_id = EncodeJob::find($id)->jobLogistic->job_id;

            if( $request->pass_btn ) {
                // Accept incoming job encoding
                $job_status_id = 16; // Physical accepted
                Job::find($job_id)->update(['job_status_id' => $job_status_id]);

                $job_log_desc = trans('cdu.accept_encode_job', ['jobNo' => sprintf('JO%08d', $job_id), 
                                                                'user' => Auth::user()->name, 
                                                                'date' => $today]);
                JobLog::setJobLog($job_id, $job_status_id, $job_log_desc, Auth::user()->id, request()->ip());

                if ($special_case) {
                    // Accepted encoded job is special case
                    $job_status_id = 33;
            
                    SpecialCase::create([
                        'job_id' => $job_id, 
                        'old_imei' => $job->imei, 
                        'created_by' => $request->user()->id,
                    ]);

                    $job->update(['job_status_id' => $job_status_id]);

                    /* Log Job */
                    $job_log_desc = trans('cdu.create_special_case', ['jobNo' => sprintf('JO%08d', $job_id), 
                                                                    'user' => Auth::user()->name, 
                                                                    'date' => $today]);
                    JobLog::setJobLog($job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());
                }
            } elseif ( $request->fail_btn ) {
                // Reject incoming job encoding
                $job_status_id = 31; // Cancelled

                Job::find($job_id)->update(['job_status_id' => $job_status_id]);

                $job_log_desc = trans('cdu.reject_encode_job', ['jobNo' => sprintf('JO%08d', $job_id), 
                                                                'user' => Auth::user()->name, 
                                                                'date' => $today,
                                                                'description' => $request->description]);
                JobLog::setJobLog($job_id, $job_status_id, $job_log_desc, Auth::user()->id, request()->ip());
            } 
        });
        
        flash(trans('validation.update_success', ['attribute' => 'encode job']), 'success');

        return redirect()->route('encode_job.index');
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
     * Accept / Deny Job Encoding at Job Encoding list.
     *
     * @param  int $id
     * @param  int $status
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function approveEncodeJob($id, $status, Request $request)
    {
        $this->authorize('encode_job');

        DB::transaction(function () use ($id, $status, $request) {
            $today = Carbon::now('Asia/Manila');

            if( $status == 1 ) {
                if( Auth::user()->company_id == 1 ) {
                    $encode_job_status = 2; // Accepted by Encoder
                } else {
                    $encode_job_status = 6; // Accepted by Branch Admin
                }
            } else {
                if( Auth::user()->company_id == 1 ) {
                    $encode_job_status = 3; // Rejected by Encoder
                } else {
                    $encode_job_status = 6; // Accepted by Branch Admin
                }
            }

            // Accept of deny encode job
            $logistic = EncodeJob::where(function ($query) use($id, $request) {
                                        $query->where('id', $id);
                                        $query->where('flag', true);
                                    })
                                    ->update([  'status' => $encode_job_status,
                                                'description' => $request->description
                                            ]);

            $job = EncodeJob::find($id)->jobLogistic->job;
            $job_id = $job->id;
            $special_case = $job->special_case;

            if( $status == 1 ) {
                // Accept incoming job encoding
                $job_status_id = 16; // Physical accepted
                Job::find($job_id)->update(['job_status_id' => $job_status_id]);

                $job_log_desc = trans('cdu.accept_encode_job', ['jobNo' => sprintf('JO%08d', $job_id), 
                                                                'user' => Auth::user()->name, 
                                                                'date' => $today]);
                JobLog::setJobLog($job_id, $job_status_id, $job_log_desc, Auth::user()->id, request()->ip());

                if ($special_case) {
                    // Accepted encoded job is special case
                    $job_status_id = 33;
            
                    SpecialCase::create([
                        'job_id' => $job_id, 
                        'old_imei' => $job->imei, 
                        'created_by' => $request->user()->id,
                    ]);

                    $job->update(['job_status_id' => $job_status_id]);

                    /* Log Job */
                    $job_log_desc = trans('cdu.create_special_case', ['jobNo' => sprintf('JO%08d', $job_id), 
                                                                    'user' => Auth::user()->name, 
                                                                    'date' => $today]);
                    JobLog::setJobLog($job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());
                }
            } else {
                // Reject incoming job encoding
                $job_status_id = 31; // Cancelled
                Job::find($job_id)->update(['job_status_id' => $job_status_id]);

                $job_log_desc = trans('cdu.reject_encode_job', ['jobNo' => sprintf('JO%08d', $job_id), 
                                                                'user' => Auth::user()->name, 
                                                                'date' => $today,
                                                                'description' => $request->description]);
                JobLog::setJobLog($job_id, $job_status_id, $job_log_desc, Auth::user()->id, request()->ip());
            }
        });
        
        flash(trans('validation.update_success', ['attribute' => 'encode job']), 'success');

        return redirect()->route('encode_job.index');
    }
}