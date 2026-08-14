<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
	
	protected $fillable = ['permission_name', 'permission_label', 'description', 'parent_id', 'flag', 'created_by', 'updated_by'];

    /*
     * A permission assigned to many roles
     */
    public function roles()
    {
    	return $this->belongsToMany(Role::class);
    }

    /**
     * Get the role's creator.
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }
}
