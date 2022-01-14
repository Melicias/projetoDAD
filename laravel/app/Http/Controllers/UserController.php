<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPostRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\VcardService;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request){
        $user = User::all()->except($request->user()->id);
		return UserResource::collection($user);
    }

    public function getAdmin(Request $request,User $user){
        return new UserResource($user);
    }

    public function update(UserPostRequest $request){
        $data = collect($request->all());
        return new UserResource((new UserService)->update($request->user(), $data));
    }

    public function delete(Request $request,User $user){
        $data = collect($request->all());
        return (new UserService)->delete($data, $user);
    }

    public function store(UserPostRequest $request){
        $data = collect($request->validated());
		$user = (new UserService)->store($data);
		return  new UserResource($user);
    }
}
