<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Vcard;
use App\Models\PaymentType;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\VcardService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\VcardResource;
use App\Http\Requests\VcardPostRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\VcardadminPostRequest;

class VcardController extends Controller
{

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \app\Http\Requests\VcardPostRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(VcardPostRequest $request)
	{
		$data = collect($request->validated());
		$vcard = (new VcardService)->store($data);
		return $vcard;
		//return new VcardResource($vcard);
	}

	public function getTransactionsData(Request $request)
	{
		$lastYearData = [
			'data' => ['DataSet1' => []],
			'months' => [],
		];
		$userTransactionData = [
			'typeC' => [],
			'typeD' => [],
			'difTypeCTypeD' => [],
		];
		$months =  [
			"Janeiro",
			"Fevereiro",
			"Março",
			"Abril",
			"Maio",
			"Junho",
			"Julho",
			"Agosto",
			"Setembro",
			"Outubro",
			"Novembro",
			"Dezembro",
		];
		$userTransactionData['typeC'] = Transaction::where("vcard", "like", $request->user()->phone_number)->where("Type", "C")->sum("value");
		$userTransactionData['typeD'] = Transaction::where("vcard", "like", $request->user()->phone_number)->where("Type", "D")->sum("value");
		$userTransactionData['difTypeCTypeD'] = round($userTransactionData['typeC'] - $userTransactionData['typeD'], 2);
		$lastYearData["months"] = $months;
		$lastMonthWithValue = 0;
		foreach ($months as $key => $month) {
			$lastYearData['data']['DataSet1'][$key] = Transaction::Date($request->user()->phone_number, $key + 1)->get();
			$aux = [];
			if (count($lastYearData['data']['DataSet1'][$key]) != 0) {
				//dd($lastYearData[$month][0]->new_balance);
				$aux = $lastYearData['data']['DataSet1'][$key][0]->new_balance;
			}
			$lastYearData['data']['DataSet1'][$key] = $aux;
			if ($lastYearData['data']['DataSet1'][$key] != []) {
				$lastMonthWithValue = $lastYearData['data']['DataSet1'][$key];
			} else {
				$lastYearData['data']['DataSet1'][$key] = $lastMonthWithValue;
			}
		}

		return response()->json(['lastYearData' => $lastYearData, 'userTransactionData' => $userTransactionData]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Vcard  $Vcard
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request)
	{
		return new VcardResource($request->user());
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \app\Http\Requests\VcardPostRequest  $request
	 * @param  Vcard  $Vcard
	 * @return \Illuminate\Http\Response
	 */
	public function update(VcardPostRequest $request)
	{
		$data = collect($request->all());
		return new VcardResource((new VcardService)->update($request->user(), $data));
	}

	/**
	 * Remove the specified resource from storage.
	 * @param  \Illuminate\Http\Request  $request
	 * @param  Vcard  $Vcard
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request)
	{
		$data = collect($request->all());

		return (new VcardService)->delete($data, $request->user());
	}
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $phone_number
	 * @return \Illuminate\Http\Response
	 */
	public function categories(Request $request)
	{
		if ($request->has("page")) {
			$categories = $request->user()->categories()->withTrashed()->orderBy('created_at')->paginate(10);
			$arr = array('data' => CategoryResource::collection($categories), 'total' => $categories->total(), 'per_page' => $categories->perPage(), 'lastPage' => $categories->lastPage());
			return response($arr, 200);
		}
		return response(CategoryResource::collection($request->user()->categories), 200);
	}

	public function transactions(Request $request)
	{
		$transactions = $request->user()->transactions();
		/* if ($request->get('search') != "") {

			$transactions = $transactions->where(function ($query) use ($request) {
				//dd($request->get('search'));
				$query
					->Where('pair_transaction', 'like', '%' . $request->get('search') . '%')
					->orWhere('type', 'like', '%' . $request->get('search') . '%')
					->orWhere('payment_type', 'like', '%' . $request->get('search') . '%');
			});
		} */
		if ($request->category != "" && $request->user()->categories()->where("id", "=", $request->category)->first()) {
			$transactions = $transactions->where(function ($query) use ($request) {
				$query->Where('category_id', '=', $request->category);
			});
		}
		if ($request->type != "") {
			$transactions = $transactions->where(function ($query) use ($request) {
				$query->Where('type', '=', $request->type);
			});
		}
		if (!empty($request->search)) {
			$searchFields = ['pair_transaction', 'payment_type'];
			$transactions->where(function ($query) use ($request, $searchFields) {
				$searchWildcard = '%' . $request->search . '%';
				foreach ($searchFields as $field) {
					$query->orWhere($field, 'LIKE', $searchWildcard);
				}
			});
		}

		switch ($request->orderBy) {
			case "0":
				$transactions = $transactions->orderby("created_at", 'DESC');
				break;
			case "1":
				$transactions = $transactions->orderby("created_at", 'ASC');
				break;
			case "2":
				$transactions = $transactions->orderby("value", 'DESC');

				break;
			case "3":
				$transactions = $transactions->orderby("value", 'ASC');
				break;
		}

		//DB::enableQueryLog();
		$transactions = $transactions->paginate(15);
		//dd($request->page)
		//dd(DB::getQueryLog()); // Show results of log

		$arr = array('data' => TransactionResource::collection($transactions), 'total' => $transactions->total(), 'per_page' => $transactions->perPage(), 'lastPage' => $transactions->lastPage());
		TransactionResource::$format = 'default';

		return response(json_encode($arr), 200);
	}

	public function tokenIsValid(Request $request)
	{
		$vcard = $request->user();
		return (new VcardService)->chekcIfTokenIsValidAndriod($vcard);
	}

	public function patch(VcardPostRequest $request)
	{
		$vcard = $request->user();
		$data = collect($request->all());
		if ($data->get("password") || $data->get("confirmation_code")) {
			if (!Hash::check(($data->get("confirmation_password")), $vcard->password)) {
				return response([
					'message' => 'password Incorrect'
				], 401);
			}
		}
		return new VcardResource((new VcardService)->patch($request->user(), $data));
	}

	public function checkContactList(Request $request)
	{
		$data = collect($request->all());
		$json = json_decode($data, true);
		for ($i = 0; $i < count($json); $i++) {
			if ($json[$i]) {
				if (substr($json[$i]["phoneNumber"], 0, 4) == "+351") {
					//dd(substr($re["phoneNumber"], 4, 13));
					$json[$i]["phoneNumber"] = substr($json[$i]["phoneNumber"], 4, 13);
				}
				$phone_number = str_replace(' ', '', $json[$i]["phoneNumber"]);
				$vcard = Vcard::find($phone_number);
				if ($vcard != null) {
					$json[$i] = array_merge($json[$i], array("vcard" => 1));
				} else {
					$json[$i] = array_merge($json[$i], array("vcard" => 0));
				}
			}
		}
		return response(json_encode($json), 200);
	}


	// THE ADMIN PART

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$vcards = Vcard::withTrashed();
		if (!empty($request->search)) {
			$searchFields = ['phone_number', 'email', 'name'];
			$vcards->where(function ($query) use ($request, $searchFields) {
				$searchWildcard = '%' . $request->search . '%';
				foreach ($searchFields as $field) {
					$query->orWhere($field, 'LIKE', $searchWildcard);
				}
			});
		}
		if (!empty($request->estado)) {
			$vcards = $vcards->where(function ($query) use ($request) {
				switch ($request->estado) {
					case "A":
						$query->Where('blocked', '=', '0');
						break;
					case "B":
						$query->Where('blocked', '=', '1');
						break;
				}
			});
		}
		if ($request->orderBy != "") {
			switch ($request->orderBy) {
				case "0":
					$vcards = $vcards->orderby("balance", 'DESC');
					break;
				case "1":
					$vcards = $vcards->orderby("balance", 'ASC');
					break;
				case "2":
					$vcards = $vcards->orderby("max_debit", 'DESC');

					break;
				case "3":
					$vcards = $vcards->orderby("max_debit", 'ASC');
					break;
			}
		}
		$vcards = $vcards->paginate(10);
		$arr = array('data' => VcardResource::collection($vcards), 'total' => $vcards->total(), 'per_page' => $vcards->perPage(), 'lastPage' => $vcards->lastPage());
		return response(json_encode($arr), 200);
	}

	public function showByPhone(Vcard $Vcard)
	{
		return new VcardResource($Vcard);
	}

	public function destroyByPhone(Request $request, Vcard $Vcard)
	{
		$data = collect($request->header());
		(new VcardService)->delete($data, $Vcard);
		return response("Vcard deleted successfully", 202);
	}

	public function destroyByPhoneSoftDelete(Request $request, Vcard $Vcard)
	{
		$data = collect($request->header());
		$respond = (new VcardService)->SoftDelete($data, $Vcard);
		return $respond;
	}

	public function updateByPhone(VcardPostRequest $request, Vcard $Vcard)
	{
		$request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'email', 'unique:vcards', 'max:255'],
			'photo_url' => ['ends_with:.png,.jpg,.jpeg'],
		]);
		$data = collect($request->all());
		return new VcardResource((new VcardService)->update($Vcard, $data));
	}

	public function updateByPhoneAdmin(VcardadminPostRequest $request, Vcard $Vcard)
	{
		$data = collect($request->all());
		return (new VcardService)->updateAdmin($Vcard, $data);
	}

	public function categoriesByPhone(Vcard $Vcard)
	{
		return response(CategoryResource::collection($Vcard->categories), 200);
	}

	public function transactionsByPhone(Vcard $Vcard)
	{
		return response(TransactionResource::collection($Vcard->transactions), 200);
	}

	public function getTransactionsDataAdmin(Request $request)
	{
		//[123,123,123,123]
		$paymentTypes = PaymentType::withTrashed()->get();
		$transactionCount = [
			'transactionsCountByC' => [],
			'transactionsCountByD' => [],
			'transactionType' => [],

		];

		foreach ($paymentTypes as $paymentType) {
			array_push($transactionCount['transactionsCountByC'], Transaction::where('payment_type', $paymentType->code)->where('Type', 'C')->count());
			array_push($transactionCount['transactionsCountByD'], Transaction::where('payment_type', $paymentType->code)->where('Type', 'D')->count());
			array_push($transactionCount['transactionType'], $paymentType->code);
		}
		$active_vcards = Vcard::where('blocked', 1)->count();
		$not_active_vcards = Vcard::where('blocked', 0)->count();
		$deleted_vcards = Vcard::withTrashed()->whereNotNull('deleted_at')->count();
		$total_balance = Vcard::sum('balance');
		$transaction_type_c = Transaction::where('Type', 'C')->count();
		$transaction_type_d = Transaction::where('Type', 'D')->count();
		$all_days = DB::select("select CAST(created_at AS DATE) as date,sum(value) as value,type from transactions group by CAST(created_at AS DATE),type");
		return response()->json([
			'vcards_blocked' => $active_vcards, 'vcards_not_blocked' => $not_active_vcards, 'deleted_vcards' => $deleted_vcards, 'total_vcards' => $deleted_vcards + $active_vcards + $not_active_vcards,
			'total_balance' => $total_balance, 'transaction_type_c' => $transaction_type_c, 'transaction_type_d' => $transaction_type_d, 'total_transaction' => $transaction_type_c + $transaction_type_d,
			'all_days' => $all_days, 'transactionCount' => $transactionCount
		]);
	}
}
