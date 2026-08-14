<?php

namespace App;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class SpecialCase extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Get job that created the special case.
     */
    public function job()
    {
        return $this->belongsTo('App\Job');
    }

    /**
     * Get info of device being service.
     */
    public function serviceDevice()
    {
        return $this->belongsTo('App\DeviceInventory', 'old_imei', 'imei');
    }

    /**
     * Get info on device being claimed.
     */
    public function claimDevice()
    {
        return $this->belongsTo('App\DeviceInventory', 'new_imei', 'imei');
    }

    /**
     * Get the special case's creator.
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get total newly assign Special Case.
     */
    public static function getTotalSpecialCase() 
    {
        $total_new_job = SpecialCase::where(function ($query) {
                                        $query->where('status', 1);
                                    })
                                    ->count();

        return $total_new_job;
    }

    /**
     * Get total expired Special Case.
     */
    public static function getTotalExpireSpecialCase() 
    {
        $expire_date = Carbon::now('Asia/Manila')->subDays(8)->toDateString();

        $total_expire_job = SpecialCase::where(function ($query) use($expire_date) {
                                            $query->where('status', 1);
                                            $query->where('created_at', '<', $expire_date);
                                        })
                                        ->count();                          
        
        return $total_expire_job;
    }
}
