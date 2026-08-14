<?php
namespace App\Http\Utilities;

use DB;

class State {

	function __construct() {
		if(!isset($_SESSION)) 
	    { 
	        session_start(); 
	    }

		if( !isset($_SESSION['states']) || empty($_SESSION['states']) ){
 			$_SESSION['states'] = DB::table('states')->where('flag', true)->orderBy('state_name', 'asc')->lists('state_name', 'id');
        	//self::$states = DB::table('states')->where('flag', true)->lists('state_name', 'id');
    	}
   	}

	/*
	Retrieve state list
	 */
	public static function all()
    {
    	return $_SESSION['states'];
    }

}
?>