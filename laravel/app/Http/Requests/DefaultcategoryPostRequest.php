<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

use function PHPUnit\Framework\isEmpty;

class DefaultcategoryPostRequest extends FormRequest
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
        if ($this->isMethod('put')) {
            $rules = [];
            if(request()->has("type")){
                $rules = array_merge($rules,array('type' => ['required','in:C,D']));
            }
            if(request()->has("name")){
                if(request()->route("id")->id != null)
                    $rules = array_merge($rules,array('name' => ['required','MIN:3','MAX:50','unique:default_categories,name,'.request()->route("id")->id.'']));
            }
            return $rules;
        }
        return [
			'type' => ['required','in:C,D'],
			'name' => ['required','MIN:3','MAX:50','unique:default_categories']
		];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return array_merge(
            parent::messages(),
            [
                'type.required' => __('The type is mandatory'),
                'name.required' => __('The name is mandatory'),
                'name.m_a_x' => __('The maximun caracters in the name is 50'),
                'type.in' => __('The type can only C and D'),
            ]
        );
    }

    /**
    * [failedValidation [Overriding the event validator for custom error response]]
    * @param  Validator $validator [description]
    * @return [object][object of various validation errors]
    */
    public function failedValidation(Validator $validator) { 
        //write your bussiness logic here otherwise it will give same old JSON response
       throw new HttpResponseException(response()->json($validator->errors(), 422)); 
   }
}
