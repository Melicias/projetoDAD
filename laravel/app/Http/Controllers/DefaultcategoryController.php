<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Defaultcategory;
use App\Services\DefaultCategoryService;
use App\Http\Resources\DefaultcategoryResource;
use App\Http\Requests\DefaultcategoryPostRequest;

class DefaultcategoryController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return Defaultcategory::withTrashed()->paginate(10);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\DefaultCategoryPostRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(DefaultCategoryPostRequest $request)
	{
		$defCat = (new DefaultCategoryService)->create($request);
		return response($defCat, 201);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Defaultcategory  $defCat
	 * @return \Illuminate\Http\Response
	 */
	public function show(Defaultcategory $id)
	{
		return new DefaultcategoryResource($id);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\DefaultcategoryPostRequest  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(DefaultcategoryPostRequest $request, DefaultCategory $id)
	{
		$defCat = (new DefaultCategoryService)->update($request, $id);
		return $defCat;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Defaultcategory $defaultcategory)
	{
		return (new DefaultCategoryService)->delete($defaultcategory);
	}
}
