<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JobTechnical extends Model
{
     /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /*
     * A job technical belongs to one job.
     */
    public function job()
    {
        return $this->belongsTo('App\Job');
    }

    /*
     * A Job Tech's Job QC.
     */
    public function qualityControl()
    {
        return $this->hasOne('App\JobQualityControl');
    }

    /**
     * Get the person assign the technical job.
     */
    public function technician()
    {
        return $this->belongsTo('App\User', 'technician_id');
    }

    /**
     * Get & set technical job repair types.
     */
    public function repairs()
    {
        return $this->belongsToMany('App\RepairType', 'job_technical_repair_type');
    }

    public function setRepairs($request)
    {
        return $this->repairs()->sync($request);
    }

    /**
     * Get & set technical job parts used.
     */
    public function parts()
    {
        return $this->belongsToMany('App\TechnicalPart', 'job_technical_part');
    }

    public function setParts($request)
    {
        return $this->parts()->sync($request);
    }

    /**
     * Get & set technical job remarks.
     */
    public function remarks()
    {
        return $this->belongsToMany('App\TechnicalRemark', 'job_technical_remark');
    }

    public function setRemarks($request)
    {
        return $this->remarks()->sync($request);
    }

    /**
     * Get the person assign the technical job.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get total newly created Job.
     */
    public static function getTotalNewJob($user_id) 
    {
        $today = Carbon::now('Asia/Manila')->toDateString();

        $total_new_job = JobTechnical::where('status', 'new')
                                    ->where('technician_id', $user_id)
                                    ->count();

        return $total_new_job;
    }

    /**
     * Get total created Job.
     */
    public static function getTotalOngoingJob($user_id) 
    {
        $today = Carbon::now('Asia/Manila')->toDateString();

        $total_wip_job = JobTechnical::where('status', 'wip')
                                    ->where('technician_id', $user_id)
                                    ->count();

        return $total_wip_job;
    }

    /**
     * Get total expired Job.
     */
    public static function getTotalExpireJob($user_id) 
    {
        $today = Carbon::now('Asia/Manila')->toDateString();

        $total_expire_job = JobTechnical::where('technician_id', $user_id)
                                    ->where('expire_date', '<', $today)
                                    ->whereNotIn('status', ['complete', 'cancel', 'pull_out'])
                                    ->count();

        return $total_expire_job;
    }
}
