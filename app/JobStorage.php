<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobStorage extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_storage';

    /**
     * Get warehouse of Job Storage.
     */
    public function warehouse()
    {
        return $this->hasOne('App\Warehouse', 'id', 'warehouse_id');
    }

    /**
     * Get details of Job being stored.
     */
    public function job()
    {
        return $this->hasOne('App\Job', 'id', 'job_id');
    }

    /**
     * Get the person storing the Job's device.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}
