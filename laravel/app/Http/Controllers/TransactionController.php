<?php

namespace App\Http\Controllers;

use App\Models\Vcard;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\VcardPostRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\TransactionPostRequest;
use App\Http\Requests\TransactionadminPostRequest;


class TransactionController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *@param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		//FAZER ALTERACAO PARA APENAS PROCURAR NAS TRANSACOES DA PESSOA $this->user()->phone_number
		$transactions = Transaction::Search($request->get('search'))->paginate(3);
		return TransactionResource::collection($transactions);
	}

	/**
	 * Display a listing of the resource.
	 */
	public function indexByUser(Request $Vcard)
	{
		//FAZER ALTERACAO PARA APENAS PROCURAR NAS TRANSACOES DA PESSOA $this->user()->phone_number
		return response($Vcard->user()->transactions, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  TransactionPostRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(TransactionPostRequest $request)
	{

		$data = collect($request->validated());
		if ($data->get("payment_type_code") == "VCARD") {
			if ($data->get("phone_number") == $data->get("payment_reference")) {
				return response([
					'mensage' => 'You cant give money to yourself'
				], 401);
			}
		}
		if (array_key_exists("confirmation_code", $request->request->all())) {
			if (!Hash::check($request->request->all()["confirmation_code"], $request->user()->confirmation_code)) {
				return response([
					'message_confirmation_code' => 'The pin code is not correct!'
				], 401);
			}
		} else {
			return response([
				'message_confirmation_code' => 'The pin code is not in the request!'
			], 401);
		}
		$transaction = (new TransactionService)->create($data);
		return response(new TransactionResource($transaction), 200);
	}




	public function transactionAdmin(TransactionadminPostRequest $request)
	{
		$data = collect($request->validated());
		$transaction = (new TransactionService)->createAdmin($data);
		return response(new TransactionResource($transaction), 200);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  TransactionPostRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function storeMobile(TransactionPostRequest $request)
	{
		$data = collect($request->validated());
		if ($data->get("payment_type_code") == "VCARD") {
			if ($data->get("phone_number") == $data->get("payment_reference")) {
				return response([
					'mensage' => 'You cant give money to yourself'
				], 401);
			}
		}

		if (array_key_exists("confirmation_code", $request->request->all())) {
			if (!Hash::check($request->request->all()["confirmation_code"], $request->user()->confirmation_code)) {
				return response([
					'message_confirmation_code' => 'The pin code is not correct!'
				], 401);
			}
		} else {
			return response([
				'message_confirmation_code' => 'The pin code is not in the request!'
			], 401);
		}
		$transaction = (new TransactionService)->createMobile($data);
		return response(new TransactionResource($transaction), 200);
	}
	/**
	 * Display the specified resource.
	 *
	 * @param  Transaction  $transaction
	 * @return \Illuminate\Http\Response
	 */
	public function show(Transaction $transaction)
	{
		TransactionResource::$format = 'default';
		if ($transaction->vcard != Request()->user()->phone_number) {
			return response("unauthorized", 401);
		}
		return new TransactionResource($transaction);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  TransactionPostRequest  $request
	 * @param  Transaction  $transaction
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Transaction $transaction)
	{
		//$transaction=($transaction->Vcard->Categories);
		$request->validate([
			'category_id' => [
				'sometimes', 'required', 'numeric',
				Rule::exists('categories', 'id')->where('Vcard', $transaction->Vcard->phone_number)
			],
			'description' => 'sometimes|required|MAX:254|String',
		]);
		$data = collect($request->all());
		return new TransactionResource((new TransactionService)->update($transaction, $data));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Transaction  $transaction
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Transaction $transaction)
	{
		return (new TransactionService)->delete($transaction) == 1 ? response("Transaction deleted", 202) : response("The Vcard associated with this transaction must be deleted first!", 400);
	}
}
