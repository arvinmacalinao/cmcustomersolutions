<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RepairType extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the technical job.
     */
    public function technicalJob()
    {
        return $this->belongsToMany('App\JobTechnical', 'job_technical_repair_type');
    }
}
