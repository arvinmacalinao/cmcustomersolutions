<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use DB;
use PDF;
use Carbon\Carbon;
use App\Http\Utilities\GlobalConstant;

use App\Job;
use App\JobTechnical;
use App\JobQualityControl;
use App\JobLog;
use App\User;

class JobQualityControlController extends Controller
{
    /**
     * Display a listing of completed Technical Job for QC.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('job_qc');

        //$expire_date = Carbon::now('Asia/Manila')->subWeeks(1)->toDateString();
        $today = Carbon::now('Asia/Manila')->toDateString();
        $limit = $request->input('limit') ? : 50;
        $imei = trim($request->input('imei'));
        $code = trim($request->input('code'));
        $status = trim($request->input('status'));
        $job_level_id = trim($request->input('job_level_id'));

        if( Auth::user()->role_id == 9 ) {
            // Retrieve QC assigned job
            $job_levels = DB::table('job_levels')->where('flag', true)->lists('name', 'id');

            $qc_jobs = JobQualityControl::where(function ($query) use($status, $today) {
                                            $query->where('user_id', Auth::user()->id);
                                            
                                            if ($status) {
                                                if( $status == 'expire' ) {
                                                    $query->where('expire_date', '<', $today)
                                                            ->whereNotIn('status', ['pass', 'fail']);
                                                } else {
                                                    $query->where('expire_date', '>', $today)
                                                            ->where('status', $status);
                                                }                                    
                                            }
                                        })
                                        ->whereHas('technicalJob.job', function ($query) use($imei) {
                                            if( $imei ) {
                                                $query->where('imei', 'like', '%'.$imei.'%');
                                            }
                                        })
                                        ->whereHas('technicalJob.job.device.inventory.model', function ($query) use($code) {
                                            if ($code) {
                                                $query->where('code', 'like', '%'.$code.'%');
                                            }
                                        })
                                        ->orderBy('id', 'desc')
                                        ->paginate($limit);
                                        //->toSql();
            //dd($qc_jobs);
            return view('jobQualityControl.indexQC', compact('qc_jobs', 'job_levels'));
        } else {
            // Retrieve Jobs that needs to be assigned to QC
            //Alvin Dela Cruz request to remove deactivate tech account Feb 15, 2019 c/o by Mary Anne Garalde
            $qc = User::where('role_id', 9)->where('company_id', Auth::user()->company_id)
                        ->where('flag',true)
                        ->orderby('name','ASC')->lists('name', 'id');

            $job_status_id = Auth::user()->company_id == 1 ? 20 : 6; // Job status where Technical Job is completed.
            
            // Retrieve completed Technical Job where Job status is completed & not yet been assigned to QC.
            $tech_jobs = JobTechnical::with('creator')
                        ->where(function ($query) {
                            $query->where('status', 'complete')->where('company_id', Auth::user()->company_id); // Job completed by Technician
                        })
                        ->whereHas('job', 
                                    function ($query) use($imei, $job_status_id) {
                                        if( $imei ) {
                                            $query->where('imei', 'like', '%'.$imei.'%');
                                        }

                                        if ( Auth::user()->company_id != 1 ) {
                                            $query->where('company_id',  Auth::user()->company_id);
                                            $query->where('job_level_id', '!=' , 3); // Level 3 job needs to be route to HQ
                                        }
                                        
                                        $query->where('job_status_id', $job_status_id);                                        
                                    })
                        ->whereDoesntHave('qualityControl')
                        ->whereHas('job.device.inventory.model', 
                                    function ($query) use($code) {
                                        if ($code) {
                                            $query->where('code', 'like', '%'.$code.'%');
                                        }
                                    })
                        ->orderBy('id', 'desc')
                        ->paginate($limit);
            
            return view('jobQualityControl.index', compact('tech_jobs', 'qc'));
        }
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
     * Assign completed Technical Job to QC.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('job_qc');

        $today = Carbon::now('Asia/Manila');
        $job_qc_status = 'new';
        $qc_name = User::where('id', $request->qc_id)->value('name');

	DB::beginTransaction();

        foreach ($request->technical_job_id as $key => $technical_job_id) {
            // Determine whether Job have been assigned to QC.
            $tech_job = JobTechnical::find($technical_job_id);
            $job_qc_exist = JobQualityControl::where('job_technical_id', $technical_job_id)->first();
            
            $job = Job::find($tech_job->job_id);

            if( !empty($tech_job) && !isset($job_qc_exist) ) {
                JobQualityControl::create([
                                            'job_technical_id' => $technical_job_id, 
                                            'user_id' => $request->qc_id, 
                                            'status' => $job_qc_status, 
                                            'expire_date' => $tech_job->job->expire_date, 
                                            'created_by' => $request->user()->id
                                        ]);

                // Update Job status
                $job_status_id = Auth::user()->company_id == 1 ? 21 : 7; 
                $job->update([
                                'job_status_id' => $job_status_id,
                                'updated_by' => $request->user()->id
                            ]);

                // Log Job 
                $job_log_desc = trans('cdu.assign_job_qc', ['jobNo' => sprintf('JO%08d', $tech_job->job_id), 
                                                            'user' => Auth::user()->name, 
                                                            'assignee' => $qc_name, 
                                                            'date' => $today]);
                JobLog::setJobLog($tech_job->job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());
            }
        }

	DB::commit();
        
        flash(trans('validation.create_success', ['attribute' => 'job assignment']), 'success');

        return redirect()->route('jobqualitycontrol.index');
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
     * Show the form for editing Job QC.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('job_qc');

        $job_tech = JobTechnical::find($id);
        //dd($job_tech);
        
        return view('jobQualityControl.edit', compact('job_tech'));
    }


    /**
     * Allow QC to update the assigned & accepted QC.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('job_qc');

        $today = Carbon::now('Asia/Manila');

        if( Auth::user()->company_id == 1 ) {
            // HQ user
            $status_pass = 23;
            $status_fail = 24;
        } else {
            // Branch user
            $status_pass = 9;
            $status_fail = 10;
        }
        
        if( $request->pass_btn ) {
            $status = 'pass';
            $job_status_id = $status_pass;
        } elseif ($request->fail_btn) {
            $status = 'fail';
            $job_status_id = $status_fail;
        } else {
            return redirect()->route('jobqualitycontrol.index');
        }

        $job_qc = JobQualityControl::where('id', $id)->where('status', 'wip')->first();
        //dd($job_qc->technicalJob->job_id);

        $job_qc->update([
                            'status' => $status,
                            'completion_date' => $today,
                            'remark' => $request->remark,
                            'updated_by' => $request->user()->id
                        ]);

        // Update Job status
        Job::find($job_qc->technicalJob->job_id)->update([
                                                            'job_status_id' => $job_status_id,
                                                            'updated_by' => $request->user()->id
                                                        ]);

        /* Log Job */
        $job_log_desc = trans('cdu.complete_job_qc', 
                                                ['user' => Auth::user()->name, 
                                                'jobNo' => sprintf('JO%08d', $job_qc->technicalJob->job_id), 
                                                'status' => $status, 
                                                'date' => $today,
                                                'remark' => $request->remark]);

        JobLog::setJobLog($job_qc->technicalJob->job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());

        flash(trans('cdu.complete_job_success', ['jobNo' => sprintf('JO%08d', $job_qc->technicalJob->job_id)]), 'success');
        
        return redirect()->route('jobqualitycontrol.index');

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
     * QC accept assigned job.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function acceptJob(Request $request)
    {
        $this->authorize('job_qc');

        $acceptance_date = Carbon::now('Asia/Manila');
        $status = 'wip';

        foreach ($request->job_qc_id as $key => $id) {
            $job_qc = JobQualityControl::where('id', $id)
                                        ->where('user_id', Auth::user()->id)
                                        ->where('status', 'new')
                                        ->first();

            if( $job_qc ) {
                $job_qc->update([
                                'acceptance_date' => $acceptance_date,
                                'status' => $status,
                                'updated_by' => $request->user()->id
                                ]);

                // Update Job status
                $job_status_id = Auth::user()->company_id == 1 ? 22 : 8;
                Job::find($job_qc->technicalJob->job_id)->update([
                                                                    'job_status_id' => $job_status_id,
                                                                    'updated_by' => $request->user()->id
                                                                ]);

                /* Log Job */
                $job_log_desc = trans('cdu.accept_job_qc', 
                                                ['user' => Auth::user()->name, 
                                                'jobNo' => sprintf('JO%08d', $job_qc->technicalJob->job_id), 
                                                'date' => $acceptance_date]);
                JobLog::setJobLog($job_qc->technicalJob->job_id, 8, $job_log_desc, $request->user()->id, $request->ip());
            }
        }

        flash(trans('cdu.accept_job_success'), 'success');

        return redirect()->back();
    }
}
