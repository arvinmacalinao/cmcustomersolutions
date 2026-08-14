<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceModel extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get the model's brand name.
     */
    public function brand()
    {
        return $this->hasOne('App\Brand', 'id', 'brand_id');
    }

    /**
     * Get the model's device type.
     */
    public function deviceType()
    {
        return $this->belongsTo('App\DeviceType');
    }

    /*
     * A model consist of 1 or many BOM items.
     */
    public function bom()
    {
        return $this->belongsToMany(Bom::class)
                    ->withPivot('category', 'created_by')
                    ->withTimestamps();
    }

    public function setBom($request)
    {
        return $this->bom()->sync($request);
    }

    /**
     * Get the model's creator.
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    /**
     * Get the imei from the model selected.
     */
    public function inventory()
    {
        return $this->belongsTo('App\DeviceInventory');
    }
}