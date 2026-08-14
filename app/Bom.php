<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bom';

    /**
     * Get the BOM's brand name.
     */
    public function brand()
    {
        return $this->hasOne('App\Brand', 'id', 'brand_id');
    }

    /*
     * A BOM has many complaints
     */
    public function models()
    {
    	return $this->belongsToMany(DeviceModel::class);
					
    }

    /*
     * A BOM (accessories) may be assigned to 0 or many jobs
     */
    /*public function jobs()
    {
        return $this->belongsToMany(Job::class);
    }*/

    /**
     * Get the BOM's creator.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}
