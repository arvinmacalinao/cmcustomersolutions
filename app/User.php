<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'role_id', 'company_id', 'flag', 'created_by', 'updated_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the user's role based on the user.
     */
    public function role()
    {
        return $this->hasOne('App\Role', 'id', 'role_id');
    }

    /**
     * Get the user's company.
     */
    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'company_id');
    }

    /**
     * Get list of jobs assigned to user.
     */
    public function jobs()
    {
        return $this->belongsToMany(Job::class);
    }

    /**
     * Get the user's creator.
     */
    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }

    /*
     * Check whether user role is a Super Admin
     */
    public function isSuperAdmin()
    {
        if ( Auth::user()->role->role_name == 'super_admin' ) {
            return true;
        }

        return false;
    }

    /*
    * Check user's current role
    */
    public function hasRole($role)
    {
        if ( is_string($role) ) {
            return $this->role->role_name == $role;
        }

        foreach ($role as $key => $r) {
            if ( $this->hasRole($r->role_name) ) {
                return true;
            }
        }

        return false;
    }
}
