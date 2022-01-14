<?php

namespace App\Http\Controllers;

use App\Http\Resources\VcardResource;
use App\Models\Piggybank;
use App\Models\Vcard;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use Error;

class PiggybankController extends Controller
{

    public function add(Request $request)
    {
        $data = collect($request->all());
        if($request->user()->balance >= $data->get("amount")){
            $request->user()->piggybank->balance = $request->user()->piggybank->balance + $data->get("amount");
            $request->user()->piggybank->update();
            $request->user()->balance = $request->user()->balance - $data->get("amount");
            $vcard = Vcard::find($request->user()->phone_number);
            if($vcard) {
                $vcard->balance = $request->user()->balance;
                $vcard->save();
            }
            return response(new VcardResource($request->user()), 201);
        }
		return response(['error' => "Its seems you dont have founds (balance)"], 400);
    }

    public function remove(Request $request)
    {
        $data = collect($request->all());
        if($request->user()->piggybank->balance >= $data->get("amount")){
            $request->user()->piggybank->balance = $request->user()->piggybank->balance - $data->get("amount");
            $request->user()->piggybank->update();
            $request->user()->balance = $request->user()->balance + $data->get("amount");
            $vcard = Vcard::find($request->user()->phone_number);
            if($vcard) {
                $vcard->balance = $request->user()->balance;
                $vcard->save();
            }
            return response(new VcardResource($request->user()), 201);
        }
		return response(['error' => "Its seems you dont have founds (piggy bank)"], 400);
    }


}