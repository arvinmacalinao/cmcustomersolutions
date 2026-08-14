<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobLogistic extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_logistic';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get Job that is being assigned to a Shipment.
     */
    public function job()
    {
        return $this->belongsTo('App\Job', 'job_id');
    }

    /**
     * Get shipment info based on device being shipped.
     */
    public function logistic()
    {
        return $this->belongsTo('App\Logistic', 'logistic_id');
    }

    /**
     * Get RDU that accepted the DO.
     */
    public function acceptedBy()
    {
        return $this->hasOne('App\User', 'id', 'encode_by');
    }

    /*
     * Get the encoder records based on the job logistic
     */
    public function encodeJobs()
    {
        return $this->hasMany('App\EncodeJob');
    }
}
