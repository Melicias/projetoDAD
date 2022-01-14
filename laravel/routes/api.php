<?php

use GuzzleHttp\Middleware;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VcardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DefaultcategoryController;
use App\Http\Controllers\PiggybankController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
	NORMAL USER ROUTES
*/

//Login
Route::post('login', [AuthController::class, 'login'])->name('login');
//create vcards
Route::post('/vcards', [VcardController::class, 'store']);
Route::get('paymenttypes', [PaymentTypeController::class, 'index']);


//Private VCards Routes
Route::group(['middleware' => ['auth:api'], 'prefix' => 'vcards'], function () {
	Route::get('/validatetoken', [VcardController::class, 'tokenIsValid']);
	Route::get('/', [VcardController::class, 'show']);
	Route::delete('/', [VcardController::class, 'destroy']);
	Route::post('/deletemobile', [VcardController::class, 'destroy']);
	Route::put('/', [VcardController::class, 'update']);
	Route::get('/transactions', [VcardController::class, 'transactions']);
	Route::get('/categories', [VcardController::class, 'categories']);
	Route::patch('/', [VcardController::class, 'patch']);
	route::get('/data', [VcardController::class, 'getTransactionsData']);
	route::post('/contactslist', [VcardController::class, 'checkContactList']);
});
//END Vcards Routes

//Private Piggybank Routes
Route::group(['middleware' => ['auth:api'], 'prefix' => 'piggybank'], function () {
	Route::post('/add', [PiggybankController::class, 'add']);
	Route::post('/remove', [PiggybankController::class, 'remove']);
});
//END Piggybank Routes

//Transactions Routes
Route::group(['middleware' => ['auth:api'], 'prefix' => 'transactions'], function () {
	Route::get('/', [TransactionController::class, 'indexByUser']);
	Route::get('/{transaction}', [TransactionController::class, 'show']);
	Route::delete('/{transaction}', [TransactionController::class, 'destroy']);
	Route::post('/', [TransactionController::class, 'store']);
	Route::post('/mobile', [TransactionController::class, 'storeMobile']);
	Route::patch('/{transaction}', [TransactionController::class, 'update']);
});
//END Transactions Routes

//Category Routes
Route::group(['middleware' => ['auth:api'], 'prefix' => 'categories'], function () {
	Route::post('/', [CategoryController::class, 'store']);
	Route::put('/{category}', [CategoryController::class, 'update']);
	Route::delete('/{category}', [CategoryController::class, 'destroy']);
});
//END Category Routes
/*
	END NORMAL USER ROUTES
*/



/*
	ADMIN ROUTES
*/
//Login
Route::post('loginadmin', [AuthController::class, 'loginadmin'])->name('loginadmin');

//User Routes   ADMIN
Route::group(['middleware' => ['auth:apiadmin'], 'prefix' => 'admin/user'], function () {
	Route::get('/', [UserController::class, 'index']);
	Route::get('/{user}', [userController::class, 'getAdmin']);
	Route::patch('/', [userController::class, 'update']);
	Route::delete('/{user}', [userController::class, 'delete']);
	Route::post('/', [userController::class, 'store']);
	//Route::put('/{user}', [VcardController::class, 'updateAdmin']);
});
//End User routes

//Private VCards Routes
Route::group(['middleware' => ['auth:apiadmin'], 'prefix' => 'admin/vcards'], function () {
	Route::get('/', [VcardController::class, 'index']);
	Route::get('/{vcard}', [VcardController::class, 'showByPhone']);
	Route::delete('/{vcard}', [VcardController::class, 'destroyByPhoneSoftDelete']);
	Route::put('/{vcard}', [VcardController::class, 'updateByPhoneAdmin']);
});
//END Vcards Routes

//DefaultCategory Routes
Route::group(['middleware' => ['auth:apiadmin'], 'prefix' => 'admin/defaultcategories'], function () {
	Route::get('/', [DefaultCategoryController::class, 'index']);
	Route::post('/', [DefaultCategoryController::class, 'store']);
	Route::get('/{id}', [DefaultCategoryController::class, 'show']);
	Route::put('/{id}', [DefaultCategoryController::class, 'update']);
	Route::delete('/{defaultcategory}', [DefaultCategoryController::class, 'destroy']);
});
//End DefaultCategory Route

//Transactions Routes
Route::group(['middleware' => ['auth:apiadmin'], 'prefix' => 'admin/transactions'], function () {
	Route::post('/', [TransactionController::class, 'transactionAdmin']);
});
//End DefaultCategory Route

//PaymentTypes Routes
/*Route::group(['middleware' => ['auth:apiadmin'], 'prefix' => 'paymenttypes'], function () {

	Route::get('/{PaymentType}', [PaymentTypeController::class, 'show']);
});*/
//END PaymentTypes Routes

//Private VCards Routes
Route::group(['middleware' => ['auth:apiadmin'], 'prefix' => 'admin/data'], function () {
	Route::get('/', [VcardController::class, 'getTransactionsDataAdmin']);
});
//END Vcards Routes
