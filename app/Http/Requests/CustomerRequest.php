<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CustomerRequest extends Request
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
        return [
            'name' => 'required|max:45',
            'email' => 'required|email|max:45|unique:customers,email,' . $this->route('customer'), 
            'gender' => 'required|in:male,female', 
            'dob' => 'date',
            'id_type' => 'required|max:20|string', 
            'id_number' => 'required|max:30|string', 
            'mobile_number' => 'max:20', 
            'home_number' => 'max:20', 
            'fax_number' => 'max:20', 
            'address' => 'required|max:250|string', 
            'postcode' => 'integer', 
            'state_id' => 'required|integer|exists:states,id',
            'country_id' => 'required|integer|exists:countries,id',
            'flag' => 'boolean', 
            'created_by' => 'integer|exists:users,id', 
            'updated_by' => 'integer|exists:users,id', 
        ];
    }
}
