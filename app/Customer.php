<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the state based on the customer.
     */
    public function state()
    {
        return $this->belongsTo('App\State');
    }

    /**
     * Get the customer's creator.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /*
     * A customer may have 1 or more device(s)
     */
    public function devices()
    {
        return $this->hasMany(DeviceRegistration::class);
    }
}
