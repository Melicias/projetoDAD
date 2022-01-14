<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserPostRequest extends FormRequest
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
		//dd($this->user()->email);
		if ($this->isMethod('patch')) {
			$rules = [];
			if(request()->has("name")){
				$rules = array_merge($rules,array('name' => ['required', 'string', 'max:190']));
			}
			if(request()->has("password")){
				$rules = array_merge($rules,array('password' => ['required', Password::defaults()]));
				$rules = array_merge($rules,array('confirmation_password' => ['required', Password::defaults()]));
			}
			return $rules;
		}
		if ($this->isMethod('put')) {
			return[
				'name' => ['required', 'string', 'max:190']
			];
		}
		return [
			'name' => ['required', 'string', 'max:190'],
			'email' => ['required', 'email', 'unique:users', 'max:190'],
			'password' => ['required', 'confirmed', Password::defaults()]
		];
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
