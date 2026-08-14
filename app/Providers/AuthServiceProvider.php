<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Company::class => CompanyPolicy::class,
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        foreach ($this->getPermissions() as $key => $permission) {
            $gate->define($permission->permission_name, function($user) use ($permission) {
                //dd($permission->roles);
                return $user->hasRole($permission->roles);
            });
        }

        $gate->define('super_admin', function($user) {
            //dd($permission->roles);
            return $user->hasRole('super_admin');
        });

        $gate->define('branch_admin', function($user) {
            //dd($permission->roles);
            return $user->hasRole('branch_admin');
        });

        $gate->define('hq_admin', function($user) {
            //dd($permission->roles);
            return $user->hasRole('hq_admin');
        });

        $gate->define('technician_hq', function($user) {
            //dd($permission->roles);
            return $user->hasRole('technician_hq');
        });

        $gate->define('technician_branch', function($user) {
            //dd($permission->roles);
            return $user->hasRole('technician_branch');
        });

        $gate->define('quality_assurance', function($user) {
            //dd($permission->roles);
            return $user->hasRole('quality_assurance');
        });
        
        /*$gate->define('show-company', function($user){
            //return true;
            return $user->id == 1;
        });
        
        $gate->define('show-e-warranty', function($user, $eWarranty){
            return true;
        })

        $gate->define('show-e-warranty-detail', function($user, $eWarranty){
            return $user->id == $eWarranty->created_by || $eWarranty->created_by == 0;
        })*/
    }

    protected function getPermissions()
    {
        return Permission::with('roles')->get();
    }
}
