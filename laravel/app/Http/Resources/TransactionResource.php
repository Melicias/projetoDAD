<?php

namespace App\Http\Resources;

use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{

	public static $format = "default";

	public function toArray($request)
	{
		switch (TransactionResource::$format) {
			case 'detailed':
				return  [
					'current_page' => $this->total(),
					'id' => $this->id,
					'Vcard' => new VcardResource($this->Vcard),
					'type' => $this->type,
					'value' => $this->value,
					'old_balance' => $this->old_balance,
					'new_balance' => $this->new_balance,
					'payment_type' => $this->payment_type,
					'payment_reference' => $this->payment_reference,
					'pair_transaction' => $this->PairTransaction,
					'pair_vcard' => new VcardResource($this->PairVcard),
					'category' => new CategoryResource($this->Category),
					'description' => $this->description,
					'date' => $this->date,
					'datetime' => $this->datetime,
				];
			default:
				return [
					'id' => $this->id,
					'Vcard' => $this->vcard,
					'type' => $this->type,
					'value' => $this->value,
					'old_balance' => $this->old_balance,
					'new_balance' => $this->new_balance,
					'payment_type' => $this->payment_type,
					'payment_reference' => $this->payment_reference,
					'pair_transaction' => $this->PairTransaction ? $this->PairTransaction->id : NULL,
					'pair_vcard' => $this->PairVcard ? $this->PairVcard->phone_number : NULL,
					'category' => new CategoryResource($this->Category),
					'description' => $this->description,
					'date' => $this->date,
					'datetime' => $this->datetime,
				];
		}
	}
}
