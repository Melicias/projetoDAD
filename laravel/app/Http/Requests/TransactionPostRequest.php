<?php

namespace App\Http\Requests;

use App\Models\Vcard;
use App\Rules\isBlocked;
use App\Models\PaymentType;
use App\Services\VcardService;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransactionPostRequest extends FormRequest
{

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		//dd(FormRequest::user());
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$this->validate([
			'payment_type_code' => ['required', 'exists:payment_types,code', 'string'],
		]);
		$this->validate([
			'phone_number' => ['required', 'digits:9', 'starts_with:9', 'exists:vcards,phone_number'],
		]);

		$Vcard = Vcard::find($this->request->get('phone_number'));
		$vCard_balance = $Vcard->balance;
		$vCard_max_debit = $Vcard->max_debit;

		$rules = [
			'phone_number' => ['required', 'digits:9', 'starts_with:9', 'exists:vcards,phone_number',new isBlocked($this->request->get('phone_number'))],
			'value' => ['required', 'numeric', 'min:0.01', 'max:999999', 'lte:' . $vCard_balance, 'lte:' . $vCard_max_debit],
			'payment_type_code' => ['required', 'exists:payment_types,code', 'string'],
			'category_id' => ['numeric',Rule::exists('categories', 'id')->where('Vcard', $Vcard->phone_number)],
			'description' => ['MAX:254', 'String'],
		];
		
		$payment_type = PaymentType::find($this->request->get('payment_type_code'));
		
		$rules['payment_reference'] = json_decode($payment_type->validation_rules, true);
		if($payment_type->code=="VCARD"){	
			$this->validate([
				'payment_reference' => ['exists:vcards,phone_number']
			]);
			if($rules['payment_reference'] != null){
				array_push($rules['payment_reference'], new isBlocked($this->request->get('payment_reference')));	
			}else{
				$rules['payment_reference'] = array(new isBlocked($this->request->get('payment_reference')));
			}
		}
		return $rules;

	}
}
