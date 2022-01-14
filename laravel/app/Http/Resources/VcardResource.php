<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VcardResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public static $format = "default";
	public function toArray($request)
	{
		switch (VcardResource::$format) {
			case 'detailed':
				return [
					'phone_number' => $this->phone_number,
					'name' => $this->name,
					'email' => $this->email,
					'balance' => $this->balance,
					'max_debit' => $this->max_debit,
					'piggy_bank' => $this->piggybank->balance,
					'blocked' => $this->blocked,
					'created_at' => $this->created_at,
					'updated_at' => $this->updated_at,
					'deleted_at' => $this->deleted_at,
				];
			default:
				return [
					'phone_number' => $this->phone_number,
					'name' => $this->name,
					'email' => $this->email,
					'balance' => $this->balance,
					'max_debit' => $this->max_debit,
					'blocked' => $this->blocked,
					'piggy_bank' => $this->piggybank->balance,
					'deleted_at' => $this->deleted_at,
				];
		}
	}
}
