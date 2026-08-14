<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use PDO;
use App\Company;
use App\DeviceModel;
use Carbon\Carbon;

class ImportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $today = Carbon::now('Asia/Manila');

        DB::setFetchMode(PDO::FETCH_ASSOC); // retrieve DB value in array instead of StdClass format.

        // Truncate tbl values.
        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        DB::table('companies')->truncate();
        DB::table('device_models')->truncate();
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");

        // Populate tbl values.
        // Populate companies tbl.
        $companies = DB::table('temp_companies')->get();

        foreach ($companies as $key => $company) {            
            $company['state_id'] = DB::table('states')->where('state_name', $company['state'])->value('id');
            $company['country_id'] = 1;

            unset($company['state']);

            Company::create($company);
        }

        // Populate device_models tbl
        $models = DB::table('temp_device_models')->get();

        foreach ($models as $key => $model) {
            $model['brand_id'] = ($model['brands'] == 'CHERRY MOBILE') ? 1 : 2;

            if ($model['device_type'] == 'Feature Phone') {
                $model['device_type_id'] = 1;
            } elseif ($model['device_type'] == 'Smart Phone') {
                $model['device_type_id'] = 2;
            } elseif ($model['device_type'] == 'Tablet') {
                $model['device_type_id'] = 3;
            } else {
                $model['device_type_id'] = 2;
            }

            unset($model['brands']);
            unset($model['device_type']);
            
            $model['flag'] = true;
            $model['created_by'] = 1;

            DeviceModel::create($model);
        }

        DB::setFetchMode(PDO::FETCH_CLASS); // revert db retrieve DB value from array to StdClass format.
    }
}