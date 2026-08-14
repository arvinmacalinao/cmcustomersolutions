<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceInventory extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['created_at', 'updated_at'];

    protected $table = 'device_inventories';

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
    public function model()
    {
        return $this->hasOne('App\DeviceModel', 'id', 'device_model_id');
    }

    /**
     * Get creator of device inventory.
     */
    public function registration()
    {
        return $this->hasOne('App\DeviceRegistration', 'imei', 'imei');
    }

    /**
     * Get creator of device inventory.
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    /**
     * Get updater of device inventory.
     */
    public function updater()
    {
        return $this->hasOne('App\User', 'id', 'updated_by');
    }
}
