<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Carbon\Carbon;

class DeviceRegistrationRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
    	//dd($this->get('id'));
    	//dd($this->request->all()['imei']);
        //dd($this->route('device_registration'));
        $tomorrow = Carbon::tomorrow('Asia/Manila');
        
        switch ($this->method()) {
        case 'GET':
        	break;
        case 'DELETE':
	        break;
        case 'POST':
            return [
                //'imei' => 'required|regex:/^[0-9]{15,19}$/|exists:device_inventories,imei|unique:device_registrations,imei',
                'imei' => 'required|alpha_num|between:15,19|exists:device_inventories,imei|unique:device_registrations,imei', 
	            'customer_id' => 'required|integer|exists:customers,id',
	            'pop_ref' => 'required|max:100',
	            'pop_date' => 'required|date|before:'.$tomorrow,
	            'warranty_date' => 'date',
	            'warranty_status' => 'integer|between:1,3',
	            'created_by' => 'integer|exists:users,id',
	            'updated_by' => 'integer|exists:users,id',
            ];
        case 'PUT':
        	return [
                //'imei' => 'required|regex:/^[0-9]{15,19}$/|exists:device_registrations,imei',
                'imei' => 'required|alpha_num|between:15,19|exists:device_registrations,imei', 
	            'customer_id' => 'required|integer|exists:customers,id',
	            'pop_ref' => 'required|max:100',
	            'pop_date' => 'required|date|before:'.$tomorrow,
                'warranty_status' => 'integer|between:1,3',
	            'warranty_date' => 'required|date',
            ];
        case 'PATCH':
        	break;
        default:
        	break;
    	}
    }
}
