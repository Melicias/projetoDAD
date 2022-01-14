<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vcard;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Defaultcategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\VcardResource;
use App\Services\DefaultCategoryService;


class UserService
{

	/**
	 * update a User.
	 *
	 * @param Collection $data
	 * @param User $user
	 * 
	 *
	 * @return User
	 */
	public function update(User $user, Collection $data)
	{
		if ($data->has("name")) {
			$user->name = $data->get("name");
		}
		if ($data->has("password")) {
			if (!Hash::check(($data->get("confirmation_password")), $user->password)) {
				return response([
					'message' => 'password Incorrect'
				], 401);
			}
			$user->password = $data->get("password");
		}
		$user->save();
		return $user;
	}

	/**
	 * delete a User.
	 *
	 * @param \Illuminate\Http\Request  $request
	 * @param User $user
	 * 
	 *
	 * @return User
	 */
	public function Delete(Collection $data, User $user)
	{
		$user->delete();
		return response("User deleted successfully", 202);
	}

	/**
	 * create a User.
	 *
	 * @param \Illuminate\Http\Request  $request
	 * @param Collection $data
	 * 
	 *
	 * @return Vcard
	 */
	public function store(Collection $data)
	{
		$user = User::create($data->all());
		return $user;
	}
}
