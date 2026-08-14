<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Auth;
use App\Http\Utilities\GlobalConstant;

use App\JobLog;

class Job extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /*
     * A job has many complaints
     */
    public function complaints()
    {
    	return $this->belongsToMany(Complaint::class);
    }

    public function setComplaints($request)
    {
        return $this->complaints()->sync($request);
    }

    /*
     * A job includes 0 or many accessories
     */
    public function accessories()
    {
        return $this->belongsToMany(Bom::class);
            //->withPivot('created_by')
            //->withTimestamps();
    }

    public function setAccessories($request)
    {
        return $this->accessories()->sync($request);
    }

    /**
     * Get the technicals for the job.
     */
    public function technicals()
    {
        return $this->hasMany('App\JobTechnical');
    }

    /**
     * Get company that creates the job.
     */
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    /**
     * Get device of the job.
     */
    public function device()
    {
        return $this->belongsTo('App\DeviceRegistration', 'imei', 'imei');
    }

    /**
     * Get job device storage warehouse.
     */
    public function storage()
    {
        return $this->hasMany('App\JobStorage');
    }

    /**
     * Get status of the job.
     */
    public function status()
    {
        return $this->belongsTo('App\JobStatus', 'job_status_id');
    }

    /**
     * Get status of the job.
     */
    public function level()
    {
        return $this->belongsTo('App\JobLevel', 'job_level_id');
    }

    /**
     * Get job's special case.
     */
    public function specialCase()
    {
        return $this->hasOne('App\SpecialCase');
    }

    /*
     * Get latest owner for the job
     */
    public function owner()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('created_by')
            ->withTimestamps();
    }

    /*
     * Get the jobs logistic records
     */
    public function logistic()
    {
        return $this->hasMany('App\JobLogistic');
    }

    /*
     * Get the jobs ticket records
     */
    public function tickets()
    {
        return $this->hasMany('App\Ticket');
    }

    /*
     * Get the jobs ticket records
     */
    public function logs()
    {
        return $this->hasMany('App\JobLog');
    }

    /**
     * Get the job's creator.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get total newly created Job.
     */
    public static function getTotalNewJob($company_id) 
    {
        $expired_date = Carbon::now('Asia/Manila')->subDays(30)->toDateString();

        $total_new_job = Job::where('created_at', '>=', $expired_date)
                                ->where(function($query) use($company_id) {
                                            if ($company_id == 1) {
                                                // retrieve HQ job list
                                                $query->where('job_status_id', 1)
                                                        ->where('company_id', $company_id);
                                            } else {
                                                $query->where('company_id', $company_id)
                                                        ->where('job_status_id', 1);
                                            }
                                        })
                                ->count();

        return $total_new_job;
    }

    /**
     * Get total expired Job.
     */
    public static function getTotalExpireJob($company_id) 
    {
        $expired_date = Carbon::now('Asia/Manila')->subDays(30)->toDateString();

        $total_expire_job = Job::whereNotBetween('job_status_id', [29, 32])
                                ->where('created_at', '<', $expired_date)
                                ->where(function($query) use($company_id) {
                                            if ($company_id == 1) {
                                                // retrieve HQ job list
                                                $query->where('company_id', $company_id)
                                                        ->orWhere(function($query) {
                                                            $query->where('job_level_id', 3)->where('job_status_id', '>=', 12);
                                                        });
                                            } else {
                                                $query->where('company_id', $company_id);
                                            }
                                        })
                                ->count();
        //dd($total_expire_job);

        return $total_expire_job;
    }

    /**
     * Get total completed Job.
     */
    public static function getTotalCompleteJob($company_id) 
    {
        $total_complete_job = Job::whereIn('job_status_id', [29, 30])
                            ->where(function($query) use($company_id) {
                                        $query->where('company_id', $company_id);
                                    })
                            ->count();

        return $total_complete_job;
    }

    /**
     * Change Job Level.
     *
     * @param  int  $id, int $job_level_id, int $prev_job_level, string $ip_address
     */
    public static function changeJobLevel($id, $job_level_id, $prev_job_level, $ip_address)
    {
        $today = Carbon::now('Asia/Manila');

        Job::find($id)->update([
                                'job_level_id' => $job_level_id,
                                'updated_by' => Auth::user()->id, 
                                ]);

        // Log Job
        JobLog::create([
                        'job_id' => $id,
                        //'process_id' => GlobalConstant::getJobProcess()['change_job_level'],
                        'job_status_id' => 6,
                        'description' => trans('cdu.change_job_level', 
                                                ['user' => Auth::user()->name, 
                                                'oldLevel' => $prev_job_level,
                                                'newLevel' => $job_level_id,
                                                'jobNo' => sprintf('JO%08d', $id), 
                                                'date' => $today]),
                        'log_by' => Auth::user()->id,
                        'ip_address' => $ip_address
                        ]);
    }
}
