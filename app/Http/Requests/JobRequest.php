<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class JobRequest extends Request
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
                'job_type' => 'required|integer|digits_between:1,2',
                'job_level_id' => 'required|integer|digits_between:1,3',
                'imei' => 'required|alpha_num|between:15,19|exists:device_registrations,imei',
                'contact_name' => 'required|max:45',
                //'mobile_number' => 'max:11|regex:[0-9]|required_if:telephone_number,',
                'mobile_number' => 'max:11|regex:/^[0-9]{0,11}$/',
                'telephone_number' => 'max:11|regex:/^[0-9]{0,11}$/',
                'case_category' => 'required',
                'note' => 'max:140',
                'complaint_id' => 'required|array'
            ];
        case 'PUT':
            return [
                'imei' => 'required|alpha_num|between:15,19|exists:device_registrations,imei',
                'customer_id' => 'required|integer|exists:customers,id',
                'case_category' => 'required',
                'pop_ref' => 'required',
                'pop_date' => 'required|date',
                'warranty_date' => 'required|date',
                'complaint_id' => 'required|array'
            ];
        case 'PATCH':
            break;
        default:
            break;
        }
    }
}
