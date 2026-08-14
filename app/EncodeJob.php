<?php

namespace App;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class EncodeJob extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /*
     * Get the job's logistic records
     */
    public function jobLogistic()
    {
        return $this->belongsTo('App\JobLogistic', 'job_logistic_id');
    }

    /**
     * Get the job encoder's details.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get total newly encode Job.
     */
    public static function getTotalEncodeJob() 
    {
        $total_new_job = EncodeJob::where(function ($query) {
                                        $query->whereNull('status');
                                    })
                                    ->count();

        return $total_new_job;
    }

    /**
     * Get total expired Job.
     */
    public static function getTotalExpireJob() 
    {
        $today = Carbon::now('Asia/Manila')->toDateString();

        $total_expire_job = EncodeJob::where(function ($query) {
                                            $query->whereNull('status');
                                        })
                                        ->whereHas('jobLogistic.job', function ($query) use($today) {
                                                    $query->where('expire_date', '<', $today);
                                                })
                                        ->count();

        return $total_expire_job;
    }
}
