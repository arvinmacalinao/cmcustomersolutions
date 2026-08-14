<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;

use App\Job;

class Warehouse extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the warehouse's company.
     */
    public function company()
    {
        return $this->belongsTo('App\Company');
    }

    /**
     * Get list of Job being stored in the Warehouse.
     */
    public function storage()
    {
        return $this->hasMany('App\JobStorage');
    }

    /**
     * Get the state based on the warehouse selected.
     */
    public function state()
    {
        return $this->belongsTo('App\State');
    }

    /**
     * Get the company's creator.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get total unstore Job.
     */
    public static function getTotalUnstoreJob($user_id) 
    {
        $total_unstore_job = Job::where(function ($query) {
                                        if ( Auth::user()->company_id != 1 ) {
                                            $query->where('company_id',  Auth::user()->company_id)
                                                ->whereIn('job_status_id', [9, 15]); // Branch QC approved, Accepted shipment from HQ.
                                        } else {
                                            $query->where('job_status_id', 23); // HQ QC approved.
                                        }
                                    })
                                ->whereDoesntHave('storage')
                                ->count();

        return $total_unstore_job;
    }

    /**
     * Get total store Job.
     */
    public static function getTotalStoreJob($user_id) 
    {
        $total_store_job = JobStorage::where('status', true)
                                        ->whereHas('warehouse', function ($query) {
                                                        $query->where('company_id', Auth::user()->company_id);
                                                    })
                                        ->count();

        return $total_store_job;
    }
}
