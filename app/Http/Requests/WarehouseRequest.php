<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class WarehouseRequest extends Request
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
        switch ($this->method()) {
        case 'GET':
            break;
        case 'DELETE':
            break;
        case 'POST':
            return [
                //'name' => 'required|max:45|unique:warehouses,name,null,id,status,1', 
                'name' => 'required|max:45', 
                'company_id' => 'required|integer|exists:companies,id', 
                'address' => 'required', 
                'postcode' => 'required', 
                'state_id' => 'required|integer|exists:states,id',
            ];
        case 'PUT':
            return [
                //'name' => 'required|max:45|unique:warehouses,name,' . $this->route('warehouse'). ',id,status,1', 
                'name' => 'required|max:45', 
                'company_id' => 'required|integer|exists:companies,id', 
                'address' => 'required', 
                'postcode' => 'required', 
                'state_id' => 'required|integer|exists:states,id',
            ];
        case 'PATCH':
            break;
        default:
            break;
        }
    }
}
