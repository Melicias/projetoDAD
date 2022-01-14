<?php

namespace App\Services;

use App\Models\Category;
use App\Models\PaymentType;
use App\Models\Transaction;
use App\Models\Vcard;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class TransactionService
{
	/**
	 * Creates a new transaction.
	 *
	 * @param String $TransactionType
	 * @param Collection $data
	 * 
	 *
	 * @return Transaction
	 */
	public function create(Collection $data)
	{
		$transaction = DB::transaction(function () use ($data) {
			$transaction = new Transaction();
			$Vcard = Vcard::findOrFail($data->get('phone_number'));
			$transaction->vcard = $Vcard->phone_number;
			$transaction->date = date('Y-m-d');
			$transaction->datetime = date('Y-m-d H:m:s');
			$transaction->type = "D";
			$transaction->value = $data->get('value');
			$transaction->old_balance = $transaction->Vcard->balance;
			$transaction->new_balance = $transaction->old_balance - $data->get('value');
			$Vcard->balance = $transaction->new_balance;
			$PaymentType = PaymentType::findOrFail($data->get('payment_type_code'));
			$transaction->payment_type = $PaymentType->code;
			$transaction->payment_reference = $data->get('payment_reference');
			if ($data->has('category_id')) {
				$category = Category::findOrFail($data->get('category_id'));
				$transaction->category_id = $category->id;
			}
			if ($data->has('description')) {

				$transaction->description = $data->get('description');
			}
			$transaction->save();
			if ($PaymentType->code == "VCARD") {
				$pairTransaction = $this->vcard($data, $transaction);
				$transaction->pair_transaction = $pairTransaction->id;
				$transaction->pair_vcard = $pairTransaction->vcard;
				$transaction->save();
				$pairTransaction->pair_transaction = $transaction->id;
				$pairTransaction->save();
			}
			$Vcard->save();
			return $transaction;
		});
		return $transaction;
	}

	/**
	 * Creates a new transaction.
	 *
	 * @param String $TransactionType
	 * @param Collection $data
	 * 
	 *
	 * @return Transaction
	 */
	public function createMobile(Collection $data)
	{
		$transaction = DB::transaction(function () use ($data) {
			$transaction = new Transaction();
			$Vcard = Vcard::findOrFail($data->get('phone_number'));
			$transaction->vcard = $Vcard->phone_number;
			$transaction->date = date('Y-m-d');
			$transaction->datetime = date('Y-m-d H:m:s');
			$transaction->type = "D";
			$transaction->value = $data->get('value');
			$transaction->old_balance = $transaction->Vcard->balance;
			$transaction->new_balance = $transaction->old_balance - $data->get('value');
			$Vcard->balance = $transaction->new_balance;
			$PaymentType = PaymentType::findOrFail($data->get('payment_type_code'));
			$transaction->payment_type = $PaymentType->code;
			$transaction->payment_reference = $data->get('payment_reference');
			if ($data->has('category_id')) {
				$category = Category::findOrFail($data->get('category_id'));
				$transaction->category_id = $category->id;
			}
			if ($data->has('description')) {

				$transaction->description = $data->get('description');
			}
			$transaction->save();
			$value_piggybank = round(ceil($data->get('value')) - $data->get('value'), 2);
			if ($Vcard->balance > $value_piggybank) {
				$Vcard->balance = $Vcard->balance - $value_piggybank;
				$Vcard->piggybank->balance = $Vcard->piggybank->balance + $value_piggybank;
				$Vcard->piggybank->update();
			}

			if ($PaymentType->code == "VCARD") {
				$pairTransaction = $this->vcard($data, $transaction);
				$transaction->pair_transaction = $pairTransaction->id;
				$transaction->pair_vcard = $pairTransaction->vcard;
				$transaction->save();
				$pairTransaction->pair_transaction = $transaction->id;
				$pairTransaction->save();
			}
			$Vcard->save();
			return $transaction;
		});
		return $transaction;
	}

	/**
	 * Creates a new transaction.
	 *
	 * @param String $TransactionType
	 * @param Collection $data
	 * 
	 *
	 * @return Transaction
	 */
	public function createAdmin(Collection $data)
	{
		$transaction = DB::transaction(function () use ($data) {
			$transaction = new Transaction();
			$Vcard = Vcard::findOrFail($data->get('phone_number'));
			$transaction->vcard = $data->get('phone_number');
			$transaction->date = date('Y-m-d');
			$transaction->datetime = date('Y-m-d H:m:s');
			$transaction->type = "C";
			$transaction->value = $data->get('value');
			$transaction->old_balance = $transaction->Vcard->balance;
			$transaction->new_balance = $transaction->old_balance + $data->get('value');
			$Vcard->balance = $transaction->new_balance;
			$PaymentType = PaymentType::findOrFail($data->get('payment_type_code'));
			$transaction->payment_type = $PaymentType->code;
			$transaction->payment_reference = $data->get('payment_reference');
			if ($data->has('category_id')) {
				$category = Category::findOrFail($data->get('category_id'));
				$transaction->category_id = $category->id;
			}
			if ($data->has('description')) {

				$transaction->description = $data->get('description');
			}
			$transaction->save();
			if ($PaymentType->code == "VCARD") {
				$pairTransaction = $this->vcard($data, $transaction);
				$transaction->pair_transaction = $pairTransaction->id;
				$transaction->pair_vcard = $pairTransaction->vcard;
				$transaction->save();
				$pairTransaction->pair_transaction = $transaction->id;
				$pairTransaction->save();
			}
			$Vcard->save();
			return $transaction;
		});
		return $transaction;
	}

	/**
	 * Creates a pait transaction.
	 *
	 *
	 * @param Collection $data
	 * 
	 *
	 * @return Transaction
	 */
	private function vcard(Collection $data, Transaction $transaction)
	{
		$pairTransaction = new Transaction();
		$Vcard = Vcard::findOrFail($data->get('payment_reference'));
		$pairTransaction->vcard = $Vcard->phone_number;
		$pairTransaction->date = date('Y-m-d');
		$pairTransaction->datetime = date('Y-m-d H:m:s');
		$pairTransaction->type = "C";
		$pairTransaction->value = $data->get('value');
		$pairTransaction->old_balance = $pairTransaction->Vcard->balance;
		$pairTransaction->new_balance = $pairTransaction->old_balance + $data->get('value');
		$Vcard->balance = $pairTransaction->new_balance;
		$PaymentType = PaymentType::findOrFail($data->get('payment_type_code'));
		$pairTransaction->payment_type = $PaymentType->code;
		$pairTransaction->payment_reference = $transaction->vcard;
		$pairTransaction->pair_vcard = $transaction->vcard;
		//$pairTransaction->category_id = $transaction->category_id;
		//$pairTransaction->description = $transaction->description;
		$pairTransaction->save();
		$Vcard->save();
		return $pairTransaction;
	}

	/**
	 * Delete a transaction.
	 *
	 *
	 * @param Transaction $transaction
	 * 
	 *
	 * @return Transaction
	 */
	public function delete(Transaction $transaction)
	{
		if ($transaction->Vcard->trashed()) {
			$transaction->delete();
			return 1;
		}
		return 0;
	}

	/**
	 * update Category and Description.
	 * 
	 * @param Transaction $transaction
	 * @param Collection $data
	 * 
	 * @return Transaction
	 * 
	 */
	public function update(Transaction $transaction, Collection $data)
	{
		//$category = Category::findOrFail($data->get('category_id'));
		$transaction->category_id = $data->get('category_id');
		$transaction->description = $data->get('description');
		$transaction->save();
		return $transaction;
	}
}
