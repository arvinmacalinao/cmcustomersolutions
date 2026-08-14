<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
	/**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get Job that is based on selected ticket.
     */
    public function job()
    {
        return $this->belongsTo('App\Job', 'job_id');
    }
    
    /**
     * Get company that created the selected ticket.
     */
    public function company()
    {
        return $this->belongsTo('App\Company', 'company_id');
    }
    
    /**
     * Get creator of selected ticket.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}
