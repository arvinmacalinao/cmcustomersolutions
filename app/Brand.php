<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the users based on the roles assigned to them.
     */
    public function models()
    {
        return $this->hasMany('App\DeviceModel');
    }

    /**
     * Get the brand's creator.
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }
}
