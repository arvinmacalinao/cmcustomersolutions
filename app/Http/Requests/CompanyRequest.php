<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Auth;

class CompanyRequest extends Request
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
        switch ($this->method()) {
        case 'GET':
            break;
        case 'DELETE':
            break;
        case 'POST':
            return [
                'company_name' => 'required|max:45|unique:companies,company_name,null,id,flag,1', 
                'company_type' => 'required|max:20|in:branch,dealer', 
                'company_prefix' => 'alpha_num|size:2', 
                'email' => 'email|max:45|unique:companies,email,null,id,flag,1', 
                'contact_number' => 'required|max:20', 
                'fax_number' => 'max:20', 
                'address' => 'required|max:250', 
                'state_id' => 'required|integer|exists:states,id',
                'country_id' => 'required|integer|exists:countries,id',
                'flag' => 'boolean', 
                'created_by' => 'integer|exists:users,id', 
                'updated_by' => 'integer|exists:users,id', 
            ];
        case 'PUT':
            return [
                'company_name' => 'required|max:45|unique:companies,company_name,' . $this->route('company'). ',id,flag,1', 
                'company_type' => 'required|max:20|in:branch,dealer', 
                'company_prefix' => 'alpha_num|size:2', 
                'email' => 'email|max:45|unique:companies,email,' . $this->route('company'). ',id,flag,1', 
                'contact_number' => 'required|max:20', 
                'fax_number' => 'max:20', 
                'address' => 'required|max:250', 
                'state_id' => 'required|integer|exists:states,id',
                'country_id' => 'required|integer|exists:countries,id',
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