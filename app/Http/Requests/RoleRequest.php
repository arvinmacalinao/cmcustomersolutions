<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class RoleRequest extends Request
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
            'role_name' => 'required|max:45|unique:roles,role_name,' . $this->route('role'), 
            'role_label' => 'required|max:45|unique:roles,role_label,' . $this->route('role'),  
            'flag' => 'boolean', 
            'created_by' => 'integer|exists:users,id',
        ];
    }
}
