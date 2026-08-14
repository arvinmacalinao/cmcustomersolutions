<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Job;

class Logistic extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the route that created the logistic.
     */
    public function routeFrom()
    {
        return $this->belongsTo('App\Company', 'company_from');
    }

    /**
     * Get the route where the logistic is shipping to.
     */
    public function routeTo()
    {
        return $this->belongsTo('App\Company', 'company_to');
    }

    /**
     * Get the creator of the Delivery Order.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get jobs belongs to logistic.
     */
    public function jobs()
    {
        return $this->hasMany('App\JobLogistic');
    }

    /**
     * Get total newly created Job.
     */
    public static function getTotalIncomingLogistic() 
    {
        $total_incoming_logistic = Logistic::where(function ($query) {
                                        $query->where('company_to', 1);
                                        $query->where('status', 1);
                                    })
                                    ->count();

        return $total_incoming_logistic;
    }

    /**
     * Get total expired Job.
     */
    public static function getTotalReadyDevice() 
    {
        $total_ready_device = JobLogistic::where(function ($query) {
                                                    $query->whereIn('status', [2, 3]);
                                                })
                                        ->whereHas('job', 
                                                function ($query) {
                                                    $query->whereIn('job_status_id', [31, 34]);
                                                })
                                        ->count();

        return $total_ready_device;
    }
}
