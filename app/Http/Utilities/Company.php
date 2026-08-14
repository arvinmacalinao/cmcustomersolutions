<?php
namespace App\Http\Utilities;

use DB;

class Company {

	protected static $companies;

	function __construct() {
		if(!isset($_SESSION)) 
	    { 
	        session_start(); 
	    }

	    $_SESSION['companies'] = DB::table('companies')
								    ->where('flag', true)
								    ->orderBy('company_name', 'asc')
								    ->lists('company_name', 'id');

		/*if( !isset($_SESSION['companies']) || empty($_SESSION['companies']) ){
 			$_SESSION['companies'] = DB::table('companies')->where('flag', true)->lists('company_name', 'id');
			//define('COMPANIES', serialize(DB::table('companies')->where('flag', true)->lists('company_name', 'id')));
        	//self::$companies = DB::table('companies')->where('flag', true)->lists('company_name', 'id');
    	}*/
   	}

	/*
	Retrieve company list
	 */
	public static function all()
    {
    	return $_SESSION['companies'];
        //return self::$companies;
    }

}
?>