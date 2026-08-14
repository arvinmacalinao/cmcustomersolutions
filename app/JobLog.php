<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobLog extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get Job that belongs to the job log.
     */
    public function job()
    {
        return $this->belongsTo('App\Job', 'job_id');
    }

    /*
     * Get log's user.
     */
    public function logBy()
    {
        return $this->hasOne('App\User', 'id', 'log_by');
    }

    /*
     * Get log's Job status.
     */
    public function status()
    {
        return $this->hasOne('App\JobStatus', 'id', 'job_status_id');
    }

     /**
     * Set Job Log.
     */
    public static function setJobLog($job_id, $job_status_id, $desc, $user_id, $ip_add) 
    {
        JobLog::create([
                        'job_id' => $job_id,
                        'job_status_id' => $job_status_id,
                        'description' => $desc,
                        'log_by' => $user_id,
                        'ip_address' => $ip_add,
                        ]);
    }
}
