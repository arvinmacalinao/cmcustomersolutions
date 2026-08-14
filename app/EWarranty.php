<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EWarranty extends Model
{
    /*protected $fillable = ['imei', 'frontliner_code', 'model', 'name', 'state', 'address', 'id_type', 'id_number', 'age', 'gender', 'email', 'mobile_number', 'status', 'created_by'];

    protected $hidden =['id'];*/

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $table = 'e_warranties';
}
