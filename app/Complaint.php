<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /*
     * A complaint may assigned to one or many jobs
     */
    public function jobs()
    {
        return $this->belongsToMany(Job::class);
    }

    /**
     * Get complaint creator.
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }
}
