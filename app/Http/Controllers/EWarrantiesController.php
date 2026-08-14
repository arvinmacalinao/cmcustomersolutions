<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Requests\EWarrantyRequest;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Gate;
use Validator;

use Carbon\Carbon;
use DB;

use App\EWarranty;
use App\DeviceInventory;
use App\DeviceModel;
use App\DeviceRegistration;
use App\Customer;

use App\Http\Utilities\GlobalConstant;

class EWarrantiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('e_warranty');

        $limit  = $request->input('limit') ? : 200;
        $imei = $request->input('imei') ? : null; 
        $model = $request->input('model') ? : null; 

        $eWarranties = EWarranty::where(
                                        function($query) use($imei, $model) {
                                            if ($imei) {
                                                $query->where('imei', 'like', '%'.$imei.'%');
                                            }
                                            
                                            if ($model) {
                                                $query->where('model', 'like', '%'.$model.'%');
                                            }
                                        })
                            ->orderBy('created_at')
                            ->paginate($limit);
        
        return view('eWarranties.index', compact('eWarranties'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Store a api resource in storage.
     *
     * @param  EWarrantyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function api(Request $request)
    {
        /*
        e_warranties status: 
        '1' => 'Registered', // Successfully register customer's device.
        '2' => 'Not Register', // Did not register customer's device.
        '3' => 'Device Not Found', // Doesn't match the IMEI & Model Code.
        */

        // TODO: Temp remove to allow CDU (Patrick) to perform testing. 20170531 from skype request
        /*if( ! $this->isClientIPAdd() ){
            return Response::json([
                'error' => true,
                'data' => 'Not authorized. IP:'.$_SERVER['REMOTE_ADDR']
                ], 403);
        }*/

        $valid_id_types = implode(",", GlobalConstant::getCustomerIDType());

        // Validate incoming eWarranty data
        $validator = Validator::make($request->all(), [
            'imei' => 'required|alpha_num|size:15|exists:device_inventories,imei',
            'frontliner_code' => 'alpha_num|max:10', 
            'model' => 'required|max:25', 
            'name' => 'required|regex:/^[\w\s]+$/|max:50', 
            'email' => 'required|email|max:40', 
            'age' => 'required|integer|max:140', 
            'gender' => 'required|alpha|max:6|in:male,female', 
            'id_type' => 'required|in:' . $valid_id_types,
            'id_number' => 'required|max:30',
            'mobile_number' => 'required|max:13',
            'address' => 'required|max:50', 
            'state' => 'required|max:20'
        ]);

        if ( $validator->fails() ) {
            return Response::json([
                        'error' => true,
                        'data' => $validator->errors()
                    ], 403);
        }

        DB::transaction(function () use ($request) {
            
            $model = $request->input('model');

            // Check validity of device existance
            $deviceExist = DeviceInventory::where('imei', $request->input('imei'))
                                            ->whereHas('model', function ($query) use($model) {
                                                    if ($ewarranty->model) {
                                                            $query->where('flag', 1)
                                                                ->where(function ($query) use($ewarranty) {
                                                                        $query->where('code', $ewarranty->model)
                                                                            ->orWhere('name', $ewarranty->model);
                                                                });                                                          
                                                    }
                                                })
                                            ->count();
            
            if( $deviceExist ) {
                // Check Customer Device Registration
                $registered = DeviceRegistration::where('imei', $request->imei)->first();

                if( !$registered ) {
                    // Calculate Device Warranty Period
                    $today = Carbon::now('Asia/Manila');
                    $warrantyDuration = DeviceInventory::where('imei', $request->imei)->first()->model->warranty;
                    $warrantyDate = date('Y-m-d', strtotime("+".$warrantyDuration." months", strtotime($today)));

                    // Retrieve / Register Customer Record
                    $customer = Customer::where('email', $request->email)->first();

                    if( !$customer ) {
                        $region = DB::table('states')->where('state_name', $request->state)->select('id', 'country_id')->first();

                        if ( is_null($region) ) {
                            $region = DB::table('states')->where('state_name', 'Other')->select('id', 'country_id')->first();
                        }

                        $customer = Customer::create([
                                        'name' => $request->name, 
                                        'email' => $request->email, 
                                        'gender' => $request->gender, 
                                        'id_type' => $request->id_type,
                                        'id_number' => $request->id_number,
                                        'mobile_number' => $request->mobile_number, 
                                        'address' => $request->address,
                                        'state_id' => $region->id,
                                        'country_id' => $region->country_id,
                                        'created_by' => 1,
                                    ]);
                    } 
                    
                    // Register Customer Device
                    DeviceRegistration::create([
                        'imei' => $request->imei, 
                        'customer_id' => $customer->id, 
                        'pop_ref' => 'eWarranty', 
                        'pop_date' => $today, 
                        'warranty_date' => $warrantyDate, 
                        'warranty_status' => 1, 
                        'created_by' => 1, 
                        'created_at' => $today,
                        'updated_by' => 1, 
                        'updated_at' => $today,
                    ]);

                    $request['status'] = 1;
                } else {
                    $request['status'] = 2; 
                }
            } else {
                $request['status'] = 3;  // Invalid model
            }
            
            $request['created_by'] = 1;
            EWarranty::create($request->all());
        });

        return Response::json([
                    'error' => false,
                    'data' => 'E warranty information have been submitted.'
                ], 201);
    }

    /**
     * Verify Incoming IP Add.
     *
     * @param  
     * @return boolean
     */
    private function isClientIPAdd()
    {
        $client  = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : null;
        $forward = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : null;
        $remote  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        $cdu_ip = "115.85.17.57"; 
        
        if(filter_var($remote, FILTER_VALIDATE_IP) && $remote == $cdu_ip) {
            return true;
        }
        
        return false;
    }

    /**
     * Register Imported eWarranty Data.
     *
     * @param  null
     * @return \Illuminate\Http\Response
     */
    public function checkImportEWarrantyDate() 
    {
        /*
        e_warranties status: 
        '1' => 'Registered', // Successfully register customer's device.
        '2' => 'Not Register', // Did not register customer's device.
        '3' => 'Device Not Found', // Doesn't match the IMEI & Model Code.
        */

        $today = Carbon::now('Asia/Manila');
        $ewarranty_list = DB::table('e_warranties')->where('status', 0)->get();
        
        DB::table('e_warranties')->where('created_at', '<', Carbon::now('Asia/Manila')->subYears(8))
                                ->update([
                                    'created_at' => $today->format('Y-m-d H:i:s'),
                                    'updated_at' => $today->format('Y-m-d H:i:s'),
                                ]);
        
        foreach ($ewarranty_list as $key => $ewarranty) {
            DB::transaction(function () use ($ewarranty, $today) {
                // Check device existance
                $deviceExist = DeviceInventory::where('imei', $ewarranty->imei)
                                                ->whereHas('model', function ($query) use($ewarranty) {
                                                    if ($ewarranty->model) {
                                                            $query->where('flag', 1)
                                                                ->where(function ($query) use($ewarranty) {
                                                                        $query->where('code', $ewarranty->model)
                                                                            ->orWhere('name', $ewarranty->model);
                                                                });                                                          
                                                    }
                                                })
                                                ->count();
                
                if( $deviceExist ) {
                    // Check Customer Device Registration
                    $registered = DeviceRegistration::where('imei', $ewarranty->imei)->first();

                    if( !$registered ) {
                        // Calculate Device Warranty Period
                        $warrantyDuration = DeviceInventory::where('imei', $ewarranty->imei)->first()->model->warranty;
                        $warrantyDate = date('Y-m-d', strtotime("+".$warrantyDuration." months", strtotime($today)));

                        // Retrieve / Register Customer Record
                        $customer = Customer::where('email', $ewarranty->email)->first();

                        if( !$customer ) {
                            // retrieve customer's inserted region info
                            $region = DB::table('states')->where('state_name', $ewarranty->state)
                                                        ->select('id', 'country_id')
                                                        ->first();

                            if ( is_null($region) ) {
                                $region = DB::table('states')->where('state_name', 'Other')
                                                            ->select('id', 'country_id')
                                                            ->first();
                            }

                            $customer = Customer::create([
                                            'name' => $ewarranty->name, 
                                            'email' => $ewarranty->email, 
                                            'gender' => $ewarranty->gender, 
                                            'id_type' => null,
                                            'id_number' => null,
                                            'mobile_number' => $ewarranty->mobile_number, 
                                            'address' => $ewarranty->address,
                                            'state_id' => $region->id,
                                            'country_id' => $region->country_id,
                                            'created_by' => 1,
                                        ]);
                        } 
                        
                        // Register Customer Device
                        DeviceRegistration::create([
                            'imei' => $ewarranty->imei, 
                            'customer_id' => $customer->id, 
                            'pop_ref' => 'eWarranty', 
                            'pop_date' => $today, 
                            'warranty_date' => $warrantyDate, 
                            'warranty_status' => 1, 
                            'created_by' => 1, 
                            'created_at' => $today,
                            'updated_by' => 1, 
                            'updated_at' => $today,
                        ]);

                        $ewarranty_status = 1;
                    } else {
                        $ewarranty_status = 2; 
                    }
                } else {
                    $ewarranty_status = 3; // Invalid model
                }
                
                EWarranty::find($ewarranty->id)->update(['status' => $ewarranty_status]);
            });
        }

        return Response::json([
                    'error' => false,
                    'data' => 'E warranty information have been submitted.'
                ], 201);
    }

}