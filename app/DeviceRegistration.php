<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceRegistration extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];

    protected $table = 'device_registrations';

    /**
    * primaryKey
    *
    * @var string
    * @access protected
    */
    protected $primaryKey = 'imei';
    public $incrementing = false;

    /**
     * Get the model for the imei selected.
     */
    public function inventory()
    {
        return $this->belongsTo('App\DeviceInventory', 'imei', 'imei');
    }

    /*
     * A device belongs to a customer.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get creator of device registration.
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    /**
     * Get updater of device registration.
     */
    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updated_by');
    }
}
