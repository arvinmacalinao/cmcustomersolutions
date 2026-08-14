<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class BrandRequest extends Request
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
            'brand_name' => 'required|max:45|unique:brands,brand_name,' . $this->route('brand'), 
            'flag' => 'boolean', 
            'created_by' => 'integer|exists:users,id', 
            'updated_by' => 'integer|exists:users,id', 
        ];
    }
}
