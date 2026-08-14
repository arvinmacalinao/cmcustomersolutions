<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ComplaintRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //return $this->authorize('complaint_mgmt');
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
            'name' => 'required|max:45|unique:complaints,name,' . $this->route('complaint'), 
            'code' => 'required|max:4|unique:complaints,code,' . $this->route('complaint'), 
            'parent_id' => 'integer|exists:complaints,id', 
            'flag' => 'boolean',
            'created_by' => 'integer|exists:users,id', 
            'updated_by' => 'integer|exists:users,id', 
        ];
    }
}
