<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Validation\Validator;

class EWarrantyRequest extends Request
{
    /**
     * Force response json type when validation fails
     * @var bool
     */
    protected $forceJsonResponse = true;

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
     *
     */
    public function rules()
    {
        return [
            //'imei' => 'required|alpha_num|between:15,19|unique:e_warranties', 
            'imei' => 'required|alpha_num|between:15,19', 
            'frontliner_code' => 'alpha_num|max:10', 
            //'model' => 'required|alpha_num|max:25|exists:models,model_name', 
            'model' => 'required|alpha_num|max:25', 
            'customer_name' => 'required|regex:/^[\w\s]+$/|max:50', 
            'age' => 'required|integer|max:140', 
            'gender' => 'required|alpha|max:6|in:male,female', 
            'email' => 'required|email|max:40', 
            'phone_number' => 'required|max:13',
            'location' => 'required|max:50', 
            'city' => 'required|max:20'
        ];

        /*return [
            'imei' => 'required'
            ];*/
    }


    /**
     * 
     * @param  Validator $validator [description]
     * @return [type]               [description]
     */
    protected function formatErrors(Validator $validator)
    {
        return $validator->errors()->all();
        
        return Response::json([
                'error' => true,
                'data' => 'Param failed validation.'
                ], 422);
    }

    /**
     * [wantsJson description]
     * @return [type] [description]
     */
    public function wantsJson()
    {
        return true;
    }


}
