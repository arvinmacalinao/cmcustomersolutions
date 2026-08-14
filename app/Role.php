<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Auth;
use Illuminate\Http\Request;

class Role extends Model
{

    protected $fillable = ['role_name', 'role_label', 'flag', 'created_by', 'updated_by'];

    /**
     * Get the users based on the roles assigned to them.
     */
    public function users()
    {
        return $this->hasMany('App\User');
    }

    /*
     * A role has many permissions
     */
    public function permissions()
    {
    	return $this->belongsToMany(Permission::class)
            		->withPivot('created_by')
            		->withTimestamps();
    }

    public function setPermission(Request $request)
    {
    	foreach ($request->permission as $key => $permission) {
            $insert_data[$permission] = ['created_by' => Auth::id()];
        }

    	return $this->permissions()->sync($insert_data);
    }

    /**
     * Get the role's creator.
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }
}
