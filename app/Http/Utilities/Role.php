<?php
namespace App\Http\Utilities;

use DB;

class Role {

	function __construct() {
		if(!isset($_SESSION)) 
	    { 
	        session_start(); 
	    }

	    $_SESSION['roles'] = DB::table('roles')->where('flag', true)->lists('role_label', 'id');

		/*if( !isset($_SESSION['roles']) || empty($_SESSION['roles']) ){
 			$_SESSION['roles'] = DB::table('roles')->where('flag', true)->lists('role_label', 'id');
        	//self::$userRoles = DB::table('user_roles')->where('flag', true)->lists('role_name', 'id');
    	}*/
   	}

	/*
	Retrieve user role list
	 */
	public static function all()
    {
    	return $_SESSION['roles'];
    }

}
?>