<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class DeviceModelRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Set user permission based on the role assigned
        // $user_role_id = User::where(Auth::id())->value('user_role_id');
        // return UserRole::where('role_name', 'admin')->where('id', user_role_id)->exists();
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|max:30|unique:device_models,code,' . $this->route('model'), 
            'name' => 'required|max:30|unique:device_models,name,' . $this->route('model'), 
            'brand_id' => 'required|integer|exists:brands,id', 
            'device_type_id' => 'required|integer|exists:device_types,id', 
            'warranty' => 'required|integer|between:0,200', 
            'price' => 'required|numeric|between:0,999999.99', 
            'labor_cost_1' => 'numeric|between:0,999999.99', 
            'labor_cost_2' => 'numeric|between:0,999999.99', 
            'labor_cost_3' => 'numeric|between:0,999999.99',
            'flag' => 'boolean'
        ];
    }
}