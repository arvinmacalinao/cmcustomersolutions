<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class JobTechnicalRequest extends Request
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
                // WIP: Currently, this module is called when CSR assign job to technician. May need to change in the future. Undecided.
                'technician_id' => 'required|integer|exists:users,id',
                'job_id' => 'required|array',
                /*'technical_remark_id' => 'array',
                'remark' => 'max:250',
                'technical_part_id' => 'required|integer|exists:technical_parts,id',
                'repair_type_id' => 'integer|exists:repair_types,id',*/
                /*'repair_category' => 'required|max:45',
                'remark' => 'required|max:250',*/


                /*'job_id' => 'required|array',
                'technician_id' => 'required|integer|exists:users,id',*/
                //'status' => 'required|alpha_num|between:15,19|exists:device_registrations,imei',
                //'expire_date' => 'required|max:45',
                //'created_by' => 'required|max:13'
            ];
        case 'PUT':
            return [
                'job_id' => 'required|integer|exists:jobs,id',
                'technical_remark_id' => 'required|array',
                'remark' => 'max:250',
                /*'technical_part_id' => 'required|array',
                'repair_type_id' => 'array',*/
                //'repair_category' => 'required|max:45',
                /*'job_id' => 'required|integer|digits_between:1,2',
                'technician_id' => 'required|integer|digits_between:1,3',
                'repair_category' => '',
                'status' => 'required|alpha_num|between:15,19|exists:device_registrations,imei',
                'acceptance_date' => '',
                'expire_date' => 'required|max:45',
                'remark' => '',
                'created_by' => 'required|max:13',
                'imei' => 'required|alpha_num|between:15,19|exists:device_registrations,imei',
                'customer_id' => 'required|integer|exists:customers,id',
                'case_category' => 'required',
                'pop_ref' => 'required',
                'pop_date' => 'required|date',
                'warranty_date' => 'required|date',
                'complaint_id' => 'required|array'*/
            ];
        case 'PATCH':
            break;
        default:
            break;
        }
    }
}
