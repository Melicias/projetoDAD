<?php

namespace App\Http\Requests;

use App\Models\Vcard;
use App\Rules\isBlocked;
use App\Models\PaymentType;
use App\Services\VcardService;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransactionadminPostRequest extends FormRequest
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

		$rules = [
			'phone_number' => ['required', 'digits:9', 'starts_with:9', 'exists:vcards,phone_number'],
			'value' => ['required', 'numeric', 'min:0.01', 'max:999999', 'lte:' . ($Vcard->max_debit - $Vcard->balance)],
			'payment_type_code' => ['required', 'exists:payment_types,code', 'string'],
			'description' => ['MAX:254', 'String'],
		];

		try {
			$payment_type = PaymentType::find($this->request->get('payment_type_code'));
			$rules['payment_reference'] = json_decode($payment_type->validation_rules, true);
			if ($payment_type->code == "VCARD") {
				$this->validate([
					'payment_reference' => ['exists:vcards,phone_number']
				]);
				array_push($rules['payment_reference'], new isBlocked($this->request->get('payment_reference')));
			}
		} catch (Exception $ex) {
		}
		return $rules;
	}
}
