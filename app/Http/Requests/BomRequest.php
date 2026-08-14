<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class BomRequest extends Request
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
                'code' => 'required|string|unique:bom,code,null,id,flag,1',
                'name' => 'required|unique:bom,name,null,id,flag,1',
                'brand_id' => 'required|integer|exists:brands,id',
                'warranty' => 'integer|max:50',
                'quantity' => 'integer',
                'normal_price' => 'numeric',
                'dealer_price' => 'numeric',
                'province_price' => 'numeric',
                'status' => 'integer|in:1,2',
            ];
        case 'PUT':
            return [
                'code' => 'required|string|unique:bom,code,' . $this->route('bom'). ',id,flag,1',
                'name' => 'required|unique:bom,name,' . $this->route('bom'). ',id,flag,1',
                'brand_id' => 'required|integer|exists:brands,id',
                'warranty' => 'integer|max:50',
                'quantity' => 'integer',
                'normal_price' => 'numeric',
                'dealer_price' => 'numeric',
                'province_price' => 'numeric',
                'status' => 'integer|in:1,2',
            ];
        case 'PATCH':
            break;
        default:
            break;
        }
    }
}
