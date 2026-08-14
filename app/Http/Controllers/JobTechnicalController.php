<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\JobTechnicalRequest;
use App\Http\Controllers\Controller;

use Auth;
use DB;
use PDF;
use Carbon\Carbon;
use App\Http\Utilities\GlobalConstant;

use App\DeviceRegistration;
use App\Job;
use App\JobTechnical;
use App\JobLog;
use App\TechnicalRemark;
use App\User;

class JobTechnicalController extends Controller
{
    /**
     * Display a listing of Jobs ready to be assign to technician / Job being assign to Technician.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $today = Carbon::now('Asia/Manila')->toDateString();
        $limit = $request->input('limit') ? : 50;
        $imei = trim($request->input('imei'));
        $code = trim($request->input('code'));
        $status = trim($request->input('status'));
        $job_level_id = trim($request->input('job_level_id'));

        if( Auth::user()->role_id == 7 || Auth::user()->role_id == 8 ) {
            $this->authorize('workshop');

            // Retrieve Job being assigned to Technician
            $job_levels = DB::table('job_levels')->where('flag', true)->lists('name', 'id');

            $tech_jobs = JobTechnical::with('job')
                        ->where(function ($query) use($status, $today) {
                            $query->where('technician_id', Auth::user()->id);
                            
                            if ($status) {
                                if( $status == 'expire' ) {
                                    $query->where('expire_date', '<', $today);
                                    $query->whereNotIn('status', ['complete', 'cancel', 'pull_out']);
                                } else {
                                    $query->where('status', $status);
                                }                                    
                            }
                        })
                        ->whereHas('job', function ($query) use($imei, $job_level_id) {
                            if ($job_level_id) {
                                $query->where('job_level_id', $job_level_id);
                            }

                            if( $imei ) {
                                $query->where('imei', 'like', '%'.$imei.'%');
                            }
                        })
                        ->whereHas('job.device.inventory.model', function ($query) use($code) {
                            if ($code) {
                                $query->where('code', 'like', '%'.$code.'%');
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->paginate($limit);

            // dd($tech_jobs);
            
            return view('jobTechnicals.indexTechnician', compact('tech_jobs', 'job_levels'));
        } else {
            $this->authorize('job_mgmt');

            // Retrieve unassigned Jobs that needs to be assigned to Technician
            // Retrieve Tech Person details: 12 = Branch Technician, 13 = HQ Technician
            //Alvin Dela Cruz request to remove deactivate tech account Feb 15, 2019 c/o by Mary Anne Garalde
            $technicians = User::whereIn('role_id', [7, 8])
                        ->where('company_id', Auth::user()->company_id)
                        ->where('flag', true)->orderby('name','ASC')->lists('name', 'id');

            // Only retrieve New Job to assign to Technician.
            $jobs = Job::with('creator', 'status')
                        ->where(function ($query) use($imei, $status, $job_level_id) {
                                    if ($job_level_id) {
                                        $query->where('job_level_id', $job_level_id);
                                    }

                                    if ($status) {
                                        $query->where('job_status_id', $status);
                                    }

                                    if( $imei ) {
                                        $query->where('imei', 'like', '%'.$imei.'%');
                                    }
                                })
                        ->where(function ($query) {
                            if( Auth::user()->company_id != 1 ) {
                                // If user is from branch, only limit job created by user's branch.
                                // If Job is not created at HQ, Limit job assignment to Level 1 & 2
                                $query->where('company_id',  Auth::user()->company_id);
                                $query->where('job_level_id', '<>', 3); 
                                $query->whereIn('job_status_id', [1, 10, 11]); 
                            } else {
                                $query->where(function($query) {
                                        $query->where('job_status_id', 1)
                                            ->where('company_id',  Auth::user()->company_id);
                                })->whereIn('job_status_id', [16, 24, 25], 'or');
                            }

                        })
                        ->whereHas('device.inventory.model', 
                                    function ($query) use($code) {
                                        if ($code) {
                                            $query->where('code', 'like', '%'.$code.'%');
                                        }
                                    })
                        ->orderBy('id', 'desc')
                        ->paginate($limit);
                        //->toSql();

            //dd($jobs);
            
            return view('jobTechnicals.index', compact('jobs', 'technicians'));
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
     * Allow admin to assign job to technician.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('job_mgmt');

        $today = Carbon::now('Asia/Manila');
        $job_tech_status = 'new';
        $technician_name = User::where('id', $request->technician_id)->value('name');

        DB::transaction(function () use ($request, $job_tech_status, $technician_name, $today) {
            foreach ($request->job_id as $key => $job_id) {
                
                // Determine whether Job have been assigned to Technician.
                $tech_job_exist = JobTechnical::where('job_id', $job_id)->whereIn('status', ['new', 'wip'])->get();
                $job = Job::find($job_id);

                if( $tech_job_exist->isEmpty() ) {
                    $job_technical = JobTechnical::create([
                                                        'job_id' => $job_id, 
                                                        'company_id' => Auth::user()->company_id,
                                                        'technician_id' => $request->technician_id, 
                                                        'status' => $job_tech_status, 
                                                        'expire_date' => $job->expire_date, 
                                                        'created_by' => $request->user()->id
                                                        ]);

                    // Update Job status
                    if( Auth::user()->company_id == 1 ) {
                        $job_status_id = 17; 
                    } else {
                        $job_status_id = 3; 
                    }

                    $job->update([
                                    'job_status_id' => $job_status_id,
                                    'updated_by' => $request->user()->id
                                ]);
                    
                    // Log Job 
                    $job_log_desc = trans('cdu.assign_job',  ['jobNo' => sprintf('JO%08d', $job_id), 
                                                            'user' => Auth::user()->name, 
                                                            'assignee' => $technician_name, 
                                                            'date' => $today]);
                    JobLog::setJobLog($job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());
                }
            }
        });

        flash(trans('validation.create_success', ['attribute' => 'job assignment']), 'success');

        return redirect()->route('jobtechnical.index');
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
     * Show selected job technician information.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('workshop');

        $tech_job = JobTechnical::where('id', $id)->where('technician_id', Auth::id())->where('status', 'wip')->firstOrFail();
        $remark_list = TechnicalRemark::where('flag', true)->lists('name', 'id');
        $job_levels = DB::table('job_levels')->where('flag', true)->lists('name', 'id');
        $selected_remarks = DB::table('job_technical_remark')->where('job_technical_id', $id)->lists('technical_remark_id');

        //dd($job_level_id);
        //dd($selected_parts);
        //dd($tech_job->job->image);
        //dd($remark_list);
        
        return view('jobTechnicals.edit', compact('tech_job', 'remark_list', 'selected_remarks', 'job_levels'));
    }


    /**
     * Update the specified Technical Job.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(JobTechnicalRequest $request, $id)
    {
        $this->authorize('workshop');

        $today = Carbon::now('Asia/Manila');
        $job = Job::find($request->job_id);
        $device = DeviceRegistration::where('imei', $job->imei)->first();
        $job_technical = JobTechnical::findOrFail($id);
        $void_warranty = ($request->void_warranty == 3 && $device->warranty_date >= $today) ? true : false;

        //dd($request->all());
        DB::transaction(function () use ($request, $today, $job, $device, $job_technical, $void_warranty) {
            // Void device warranty
            if( $void_warranty ) {
                $device->update([
                                    'warranty_status' => 3,
                                    'updated_by' => Auth::user()->id
                                ]);

                $job->update([
                                'warranty' => 3,
                                'updated_by' => Auth::user()->id
                            ]);

                /* Log Job */
                $job_log_desc = trans('cdu.job_void_device_warranty', 
                                                        ['user' => Auth::user()->name, 
                                                        'imei' => $device->imei,
                                                        'jobNo' => sprintf('JO%08d', $request->job_id), 
                                                        'date' => $today]);
                JobLog::setJobLog($request->job_id, $job->job_status_id, $job_log_desc, $request->user()->id, $request->ip());
            }

            // Change Job Level
            if( $job->job_level_id != 3 && $request->job_level_id != $job->job_level_id ) {
                Job::changeJobLevel($request->job_id, $request->job_level_id, $job->job_level_id, $request->ip());
            }

            // Determine whether to update, pullout or complete tech job
            if( $request->update_btn ) {
                $job_technical->update([
                                            'remark' => $request->remark,
                                            'void_warranty' => $void_warranty,
                                            'updated_by' => Auth::id()
                                        ]);

                // Set repair type
                /*if( $request->repair_type_id ) {
                    $job_technical->setRepairs($request->repair_type_id);
                }

                // Set technical parts
                if( $request->technical_part_id ) {
                    $job_technical->setParts($request->technical_part_id);
                }*/

                // Set remarks for technical job
                if( $request->technical_remark_id ) {
                    $job_technical->setRemarks($request->technical_remark_id);
                }

                /* Log Job */
                $tech_remark_list = GlobalConstant::getTechnicalRemarks();
                $tech_remarks = $request->remark;

                if( $request->technical_remark_id ) {
                    foreach( $request->technical_remark_id as $remark_id ) {
                        if ( empty($tech_remarks) ) {
                            if ( last($request->technical_remark_id) ) {
                                $tech_remarks .= $tech_remark_list[$remark_id];
                            } else {
                                $tech_remarks .= $tech_remark_list[$remark_id] . ', ';
                            }
                            
                        } else {
                            $tech_remarks .= ', ' . $tech_remark_list[$remark_id];
                        }
                    }
                }

                $job_log_desc = trans('cdu.update_job_technical', ['user' => Auth::user()->name, 
                                                                    'jobNo' => sprintf('JO%08d', $request->job_id), 
                                                                    'date' => $today,
                                                                    'remarks' => $tech_remarks]);
                JobLog::setJobLog($request->job_id, $job->job_status_id, $job_log_desc, $request->user()->id, $request->ip());
                
                flash(trans('cdu.update_tech_job_success', ['jobNo' => sprintf('JO%08d', $request->job_id)]), 'success');
            } elseif ($request->pullout_btn) {
                // Technician pull out from Technical Job assigned.
                $tech_job_status = 'pull_out';

                $current_job = Job::find($request->job_id);
                if ( $current_job->company_id == 1 || $request->user()->company_id == 1 ) {
                    $job_status_id = 16; // Physical Accepted
                } else {
                    $job_status_id = 1; // New
                }
                //dd($current_job);
                
                $job_technical->update([
                                            'remark' => $request->remark,
                                            //'completion_date' => $today,
                                            'void_warranty' => $void_warranty,
                                            'status' => $tech_job_status,
                                            'updated_by' => Auth::id()
                                        ]);

                // Set remarks for technical job
                if( $request->technical_remark_id ) {
                    $job_technical->setRemarks($request->technical_remark_id);
                }

                // Update Job status
                $current_job->update([
                                        'job_status_id' => $job_status_id,
                                        'updated_by' => $request->user()->id
                                    ]);

                /* Log Job */
                $tech_remark_list = GlobalConstant::getTechnicalRemarks();
                $tech_remarks = $request->remark;

                if( $request->technical_remark_id ) {
                    foreach( $request->technical_remark_id as $remark_id ) {
                        if ( empty($tech_remarks) ) {
                            if ( last($request->technical_remark_id) ) {
                                $tech_remarks .= $tech_remark_list[$remark_id];
                            } else {
                                $tech_remarks .= $tech_remark_list[$remark_id] . ', ';
                            }
                            
                        } else {
                            $tech_remarks .= ', ' . $tech_remark_list[$remark_id];
                        }
                    }
                }
                
                $job_log_desc = trans('cdu.pull_out_job_technical', ['user' => Auth::user()->name, 
                                                                    'jobNo' => sprintf('JO%08d', $request->job_id), 
                                                                    'date' => $today,
                                                                    'remarks' => $tech_remarks]);
                JobLog::setJobLog($request->job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());

                flash(trans('cdu.pull_out_job_success', ['jobNo' => sprintf('JO%08d', $request->job_id)]), 'success');
            } else {
                // Technician complete Technical Job Assigned
                $tech_job_status = 'complete';
                //dd($request->user());

                $job_technical->update([
                                            'remark' => $request->remark,
                                            'completion_date' => $today,
                                            'void_warranty' => $void_warranty,
                                            'status' => $tech_job_status,
                                            'updated_by' => Auth::id()
                                        ]);

                // Set repair type
                /*if( $request->repair_type_id ) {
                    $job_technical->setRepairs($request->repair_type_id);
                }

                // Set technical parts
                if( $request->technical_part_id ) {
                    $job_technical->setParts($request->technical_part_id);
                }*/

                // Set remarks for technical job
                if( $request->technical_remark_id ) {
                    $job_technical->setRemarks($request->technical_remark_id);
                }

                // Update Job status
                if( Auth::user()->company_id == 1 ) {
                    $job_status_id = 20; // HQ Tech Job Complete Status
                } else {
                    $job_status_id = 6; // Tech Job Complete Status
                }

                Job::find($request->job_id)->update([
                                                    'job_status_id' => $job_status_id,
                                                    'updated_by' => $request->user()->id
                                                ]);
                
                /* Log Job */
                $tech_remark_list = GlobalConstant::getTechnicalRemarks();
                $tech_remarks = $request->remark;

                if( $request->technical_remark_id ) {
                    foreach( $request->technical_remark_id as $remark_id ) {
                        if ( empty($tech_remarks) ) {
                            if ( last($request->technical_remark_id) ) {
                                $tech_remarks .= $tech_remark_list[$remark_id];
                            } else {
                                $tech_remarks .= $tech_remark_list[$remark_id] . ', ';
                            }
                            
                        } else {
                            $tech_remarks .= ', ' . $tech_remark_list[$remark_id];
                        }
                    }
                }

                $job_log_desc = trans('cdu.complete_job_technical', ['user' => Auth::user()->name, 
                                                                    'jobNo' => sprintf('JO%08d', $request->job_id), 
                                                                    'date' => $today,
                                                                    'remarks' => $tech_remarks]);
                JobLog::setJobLog($request->job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());

                flash(trans('cdu.complete_job_success', ['jobNo' => sprintf('JO%08d', $request->job_id)]), 'success');
            }
        });
        
        return redirect()->route('jobtechnical.index');
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
     * Technician accept assigned technical job.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function acceptJob(Request $request)
    {
        $this->authorize('workshop');

        $acceptance_date = Carbon::now('Asia/Manila');
        $job_tech_status = 'wip';

        foreach ($request->job_id as $key => $job_id) {
            $job_technical = JobTechnical::where('job_id', $job_id)
                                        ->where('company_id', Auth::user()->company_id)
                                        ->where('technician_id', Auth::user()->id)
                                        ->where('status', 'new')
                                        ->first();

            if( $job_technical ) {
                $job_technical->update([
                                        'acceptance_date' => $acceptance_date,
                                        'status' => $job_tech_status,
                                        'updated_by' => $request->user()->id
                                        ]);

                // Update Job status
                if( Auth::user()->company_id == 1 ) {
                    $job_status_id = 18;
                } else {
                    $job_status_id = 4;
                }
                
                Job::find($job_id)->update([
                                            'job_status_id' => $job_status_id,
                                            'updated_by' => $request->user()->id
                                            ]);
                
                /* Log Job */
                $job_log_desc = trans('cdu.accept_job_technical', 
                                                        ['user' => Auth::user()->name, 
                                                        'jobNo' => sprintf('JO%08d', $job_id), 
                                                        'date' => $acceptance_date]);
                JobLog::setJobLog($job_id, $job_status_id, $job_log_desc, $request->user()->id, $request->ip());
            }
        }

        flash(trans('cdu.accept_job_success'), 'success');

        return redirect()->back();
    }


    /**
     * Generate Technical Report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTechnicalForm($id)
    {
        $this->authorize('workshop');

        $technical = JobTechnical::find($id);

        //dd($technical->remarks);

        $pdf = PDF::loadView('forms.technical', compact('technical'));
        $pdf->setPaper('A4', 'potrait');
        return $pdf->stream();
    }


    /**
     * Cancel Tech Job.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected function cancelTechJob($id, Request $request)
    {
        $this->authorize('reassign_job');

        $today = Carbon::now('Asia/Manila');
        //dd(Auth::id());

        DB::transaction(function () use ($id, $today, $request) {
            // Update Permission List to allow assignment of job transfer
            // Check whether user have the permission to cancel tech job (fulfill job transfer permission, job location & job status)
            // Update tech job status
            Job::find($id)->update([
                                        'job_status_id' => 1,
                                        'updated_by' => Auth::id()
                                    ]);
            
            $technical_jobs = JobTechnical::where('job_id', $id)
                                            ->whereNotIn('status', ['complete', 'cancel', 'pull_out'])
                                            ->get();
            
            /* Log Job */
            $technical_job_id = "";
            foreach($technical_jobs as $technical_job) {
                $technical_job->update([
                                            'status' => 'cancel',
                                            'updated_by' => Auth::id()
                                        ]);
                
                if( $technical_jobs->last() ) {
                    $technical_job_id .= $technical_job->id;
                } else {
                    $technical_job_id .= $technical_job->id . ", ";
                }
            }
            
            $job_log_desc = trans('cdu.cancel_tech_job', ['jobTechnicalId' => $technical_job_id, 
                                                        'user' => Auth::user()->name, 
                                                        'date' => $today]);
            JobLog::setJobLog($request->id, 31, $job_log_desc, Auth::id(), $request->ip());
            
            flash(trans('validation.cancel_tech_job', ['jobTechnicalId' => $technical_job_id]), 'success');
        });
        
        return redirect()->route('job.index');
    }
}
