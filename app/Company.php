<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the state based on the company selected.
     */
    public function state()
    {
        return $this->belongsTo('App\State');
    }

    /**
     * Get list of jobs created by company.
     */
    public function jobs()
    {
        return $this->hasMany('App\Job');
    }

    /**
     * Get company that creates the job.
     */
    public function routeFrom()
    {
        return $this->hasMany('App\Logistic');
    }

    /**
     * Get company that creates the job.
     */
    public function routeTo()
    {
        return $this->hasMany('App\Logistic');
    }

    /**
     * Get the company's creator.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}
