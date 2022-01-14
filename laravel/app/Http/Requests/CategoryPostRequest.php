<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CategoryPostRequest extends FormRequest
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
            $rules = ['vcard' => ['required', 'digits:9', 'starts_with:9']];
            if(request()->has("type")){
                $rules = array_merge($rules,array('type' => ['required', 'in:C,D']));
            }
            if(request()->has("name")){
				$rules = array_merge($rules,array('name' => ['required', 'MAX:50', 'MIN:3']));
            }
            return $rules;
        }
		return [
			'vcard' => ['required', 'digits:9', 'starts_with:9'],
			'type' => ['required', 'in:C,D'],
			'name' => ['required', 'MAX:50','MIN:3']
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
				'vcard.required' => __('The vcard number is mandatory'),
				'name.m_a_x' => __('The maximun caracters in the name is 50'),
				'type.in' => __('The type can only C and D')
			]
		);
	}

	/**
	 * [failedValidation [Overriding the event validator for custom error response]]
	 * @param  Validator $validator [description]
	 * @return [object][object of various validation errors]
	 */
	public function failedValidation(Validator $validator)
	{
		//write your bussiness logic here otherwise it will give same old JSON response
		throw new HttpResponseException(response()->json($validator->errors(), 422));
	}
}
