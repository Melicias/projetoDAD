<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VcardPostRequest extends FormRequest
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
				$rules = array_merge($rules,array('name' => ['required', 'string', 'max:255']));
			}
			if(request()->has("email")){
				$rules = array_merge($rules,array('email' => ['required', 'email', \Illuminate\Validation\Rule::unique('vcards')->ignore($this->user()->phone_number,'phone_number'), 'max:255']));
			}
			if(request()->has("photo_url")){
				$rules = array_merge($rules,array('photo_url' => ['ends_with:.png,.jpg,.jpeg']));
			}
			if(request()->has("password")){
				$rules = array_merge($rules,array('password' => ['required', Password::defaults()]));
			}
			if(request()->has("confirmation_code")){
				$rules = array_merge($rules,array('confirmation_code' => ['required', 'digits:4']));
			}
			return $rules;
		}
		if ($this->isMethod('put')) {
			return[
				'name' => ['required', 'string', 'max:255'],
				'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('vcards')->ignore($this->user()->phone_number,'phone_number'), 'max:255'],
				'photo_url' => ['ends_with:.png,.jpg,.jpeg'],
			];
		}
		return [
			'phone_number' => ['unique:vcards', 'required', 'digits:9', 'starts_with:9'],
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'email', 'unique:vcards', 'max:255'],
			'photo_url' => ['ends_with:.png,.jpg,.jpeg'],
			'password' => ['required', 'confirmed', Password::defaults()],
			'confirmation_code' => ['required', 'digits:4']
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
