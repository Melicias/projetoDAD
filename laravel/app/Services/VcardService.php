<?php

namespace App\Services;

use App\Models\Vcard;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Defaultcategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\VcardResource;
use App\Services\DefaultCategoryService;
use App\Http\Resources\TransactionResource;
use App\Models\Piggybank;

class VcardService
{

	/**
	 * delete a Vcard.
	 *
	 * @param \Illuminate\Http\Request  $request
	 * @param Vcard $Vcard
	 * 
	 *
	 * @return Vcard
	 */
	public function Delete(Collection $data, Vcard $Vcard)
	{
		if (Hash::check($data->get('password'), $Vcard->password) || Hash::check($data->get('confirmationcode'), $Vcard->confirmation_code)) {
			if ($Vcard->balance != 0) {
				return response(['error' => "The VCard Balance must be 0.00 to be deleted."], 400);
			}
			if ($Vcard->piggybank->balance != 0) {
				return response(['error' => "The PiggyBank Balance must be 0.00 to be deleted."], 400);
			}
			if (Transaction::where('vcard', '=', $Vcard->phone_number)->exists()) {
				$Vcard->piggybank()->delete();
				$Vcard->delete();
			} else {
				$Vcard->piggybank()->forceDelete();
				$Vcard->categories()->forceDelete();
				$Vcard->forceDelete();
			}
			return response("Vcard deleted successfully", 202);
		}else{
			return response(['error' => "The password or confirmation_code are not correct!"], 400);
		}
	}

	/**
	 * delete a Vcard.
	 *
	 * @param \Illuminate\Http\Request  $request
	 * @param Vcard $Vcard
	 * 
	 *
	 * @return Vcard
	 */
	public function SoftDelete(Collection $data, Vcard $Vcard)
	{
		if ($Vcard->balance != 0) {
			return response(['error' => "The VCard Balance must be 0.00 to be deleted."], 400);
		}
		if ($Vcard->piggybank->balance != 0) {
			return response(['error' => "The PiggyBank Balance must be 0.00 to be deleted."], 400);
		}
		if (Transaction::where('vcard', '=', $Vcard->phone_number)->exists()) {
			$Vcard->piggybank()->delete();
			$Vcard->delete();
		} else {
			$Vcard->piggybank()->forceDelete();
			$Vcard->categories()->forceDelete();
			$Vcard->forceDelete();
		}
		return response("Vcard deleted successfully", 202);
	}


	/**
	 * create a Vcard.
	 *
	 * @param \Illuminate\Http\Request  $request
	 * @param Collection $data
	 * 
	 *
	 * @return Vcard
	 */
	public function store(Collection $data)
	{
		$Vcard = DB::transaction(function () use ($data) {
			Vcard::create($data->all());
			$Vcard = Vcard::find($data->get("phone_number"));
			(new DefaultCategoryService)->createAllDefaultCategories($Vcard->phone_number);
			$piggybank=["vcard" => $data->get("phone_number")];
			Piggybank::create($piggybank);
			$accessToken = $Vcard->createToken('authToken')->accessToken;
			return response([
				'vcard' => new VcardResource($Vcard), 
				'access_token' => $accessToken,
				'piggy_bank' => 0,
				'last_transaction' => null
			],200);
		});
		return $Vcard;
	}

	/**
	 * update a Vcard.
	 *
	 * @param Collection $data
	 * @param Vcard $Vcard
	 * 
	 *
	 * @return Vcard
	 */
	public function update(Vcard $Vcard, Collection $data)
	{
		$Vcard->photo_url = $data->get("photo_url");
		$Vcard->name = $data->get("name");
		$Vcard->email = $data->get("email");
		$Vcard->save();
		return $Vcard;
	}

	public function updateAdmin(Vcard $Vcard, Collection $data)
	{
		if($data->has("max_debit")){
			if($data->get("max_debit") < $Vcard->balance){
				return response("The max debit cant be lower than the user balance!",400);
			}
			$Vcard->max_debit = $data->get("max_debit");
		}
		if($data->has("blocked")){
			$Vcard->blocked = $data->get("blocked");
		}
		$Vcard->save();
		return response(new VcardResource($Vcard),201);
	}

	public function chekcIfTokenIsValidAndriod(Vcard $vcard){
		if ($vcard->blocked == 1) {
			return response(['blocked' => 1], 405);
		}
		return response([
			'vcard' => new VcardResource($vcard), 
			'piggy_bank' => 0,
			'last_transaction' => Transaction::Where('vcard',$vcard->phone_number)->orderByDesc("created_at")->first()
		], 200);
	}

	/**
	 * update a Vcard.
	 *
	 * @param Collection $data
	 * @param Vcard $Vcard
	 * 
	 *
	 * @return Vcard
	 */
	public function patch(Vcard $Vcard, Collection $data)
	{
		if($data->get("photo_url"))
			$Vcard->photo_url = $data->get("photo_url");
		if($data->get("name"))
			$Vcard->name = $data->get("name");
		if($data->get("email"))
			$Vcard->email = $data->get("email");
		if($data->get("password"))
			$Vcard->password = $data->get("password");
		if($data->get("confirmation_code"))
			$Vcard->confirmation_code = $data->get("confirmation_code");
		$Vcard->save();
		return $Vcard;
	}
	
}
