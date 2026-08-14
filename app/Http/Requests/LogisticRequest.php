<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class LogisticRequest extends Request
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
                'waybill_number' => 'required|alpha_num',
                'attention_to' => 'required',
                'email' => 'required|email',
                'contact_number' => 'required|alpha_num|max:20',
                'remark' => 'required',
                'address' => 'required',
                //'postcode' => 'required',
                'state_id' => 'required|integer|exists:states,id',
            ];
        case 'PUT':
            break;
        case 'PATCH':
            break;
        default:
            break;
        }
    }
}
