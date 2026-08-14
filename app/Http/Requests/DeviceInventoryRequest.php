<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DeviceInventoryRequest extends Request
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
        //dd($this->route('device_inventory'));

        switch ($this->method()) {
        case 'GET':
            break;
        case 'DELETE':
            break;
        case 'POST':
            return [
                //'imei' => 'required|regex:/^[0-9]{15,19}$/|unique:device_inventories,imei', 
                'imei' => 'required|alpha_num|between:15,19|unique:device_inventories,imei', 
                'device_model_id' => 'required|integer|exists:device_models,id', 
                'flag' => 'boolean', 
                'created_by' => 'integer|exists:users,id', 
                'updated_by' => 'integer|exists:users,id', 
            ];
        case 'PUT':
            return [
                //'imei' => 'required|regex:/^[0-9]{15,19}$/', 
                'imei' => 'required|alpha_num|between:15,19', 
                'device_model_id' => 'required|integer|exists:device_models,id', 
                'flag' => 'boolean', 
                'created_by' => 'integer|exists:users,id', 
                'updated_by' => 'integer|exists:users,id', 
            ];
        case 'PATCH':
            break;
        default:
            break;
        }

        
    }
}
