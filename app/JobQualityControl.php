<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JobQualityControl extends Model
{
	/**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $table = 'job_qc';

    /*
     * A Job OC has only 1 Job Tech.
     */
    public function technicalJob()
    {
    	return $this->hasOne('App\JobTechnical', 'id', 'job_technical_id');
    }

    /**
     * Get the person assign the qc job.
     */
    public function qualityController()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * Get total newly created Job QC.
     */
    public static function getTotalNewJob($user_id) 
    {
        $today = Carbon::now('Asia/Manila')->toDateString();

        $total_new_job = JobQualityControl::where('status', 'new')
                                            ->where('user_id', $user_id)
                                            ->where('expire_date', '>', $today)
                                            ->count();

        return $total_new_job;
    }

    /**
     * Get total ongoing Job QC.
     */
    public static function getTotalOngoingJob($user_id) 
    {
        $today = Carbon::now('Asia/Manila')->toDateString();

        $total_wip_job = JobQualityControl::where('status', 'wip')
                                            ->where('user_id', $user_id)
                                            ->where('expire_date', '>', $today)
                                            ->count();

        return $total_wip_job;
    }

    /**
     * Get total expired Job QC.
     */
    public static function getTotalExpireJob($user_id) 
    {
        $today = Carbon::now('Asia/Manila')->toDateString();

        $total_expire_job = JobQualityControl::where('user_id', $user_id)
                                                ->where('expire_date', '<', $today)
                                                ->whereNotIn('status', ['fail', 'pass'])
                                                ->count();

        return $total_expire_job;
    }
}
