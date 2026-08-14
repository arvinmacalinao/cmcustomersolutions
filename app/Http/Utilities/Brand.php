<?php
namespace App\Http\Utilities;

use DB;

class Brand {

	//protected static $brands;

	function __construct() {
		if(!isset($_SESSION)) 
	    { 
	        session_start(); 
	    }
	    
		$_SESSION['brands'] = DB::table('brands')->where('flag', true)->lists('name', 'id');

		/*if( !isset($_SESSION['brands']) || empty($_SESSION['brands']) ){
 			$_SESSION['brands'] = DB::table('brands')->where('flag', true)->lists('name', 'id');
			//define('COMPANIES', serialize(DB::table('companies')->where('flag', true)->lists('company_name', 'id')));
        	//self::$companies = DB::table('companies')->where('flag', true)->lists('company_name', 'id');
    	}*/
   	}

	/*
	Retrieve brand list
	 */
	public static function all()
    {
    	return $_SESSION['brands'];
        //return self::$companies;
    }

}
?>