<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PermissionRequest extends Request
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
        $permissionId = $this->route('permission');
        //dd($permissionId);
        return [
            'permission_name' => 'required|max:45|unique:permissions,permission_name,' . $this->route('permission'),
            'permission_label' => 'required|max:45|unique:permissions,permission_label,' . $this->route('permission'),
            'description' => 'max:140',
            'parent_id' => 'not_in:'.$this->route('permission'),
            'flag' => 'boolean', 
            'created_by' => 'integer|exists:users,id', 
            'updated_by' => 'integer|exists:users,id', 
        ];
    }
}
