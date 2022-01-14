<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vcard;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\VcardResource;

class AuthController extends Controller
{
	public function login(Request $request)
	{
		//check phone number
		$vcard = Vcard::where('phone_number', $request->phone_number)->first();
		if (!$vcard) {
			return response([
				'message' => 'Number not found'
			], 401);
		}
		if (!Hash::check(($request->password), $vcard->password)) {
			return response([
				'message' => 'password Incorrect'
			], 401);
		}
		if ($vcard->blocked == 1) {
			return response([
				'message' => 'Account blocked'
			], 401);
		}
		//delete previous tokens
		$vcard->tokens()->delete();
		$token = $vcard->createToken('authToken')->accessToken;
		return response([
			'vcard' => new VcardResource($vcard),
			'access_token' => $token,
			'piggy_bank' => 0,
			'last_transaction' => Transaction::Where('vcard', $vcard->phone_number)->orderByDesc("created_at")->first()
		], 200);
	}

	public function loginadmin(Request $request)
	{
		//check email
		$admin = User::where('email', $request->email)->first();
		if (!$admin || !Hash::check(($request->password), $admin->password)) {
			return response([
				'message' => 'bad credentials'
			],401);
		}
		//delete previous tokens
		$admin->tokens()->delete();
		$token = $admin->createToken('authToken')->accessToken;
		return response(['admin' => new UserResource($admin), 'access_token' => $token], 200);
	}
}
