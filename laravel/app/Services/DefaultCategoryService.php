<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Defaultcategory;
use App\Http\Resources\DefaultCategoryResource;
use App\Http\Requests\DefaultcategoryPostRequest;
use Exception;

class DefaultCategoryService
{
	/**
	 * create all default categories into a vcard
	 *
	 * @param number $Vcard
	 * 
	 *
	 * @return
	 */
	public function createAllDefaultCategories($phone_number)
	{
		$defaultCat = Defaultcategory::all();
		foreach ($defaultCat as $key => $value) {
			Category::create(['vcard' => $phone_number, 'type' => $value->type, 'name' => $value->name]);
		}
	}

	/**
	 * get a DefaultCategory.
	 *
	 * @param DefaultCategory $defCat
	 *
	 * @return DeafultCategoryResource
	 */
	public function getDefaultCategory($id)
	{
		if ($id != null)
			return response(new DefaultcategoryResource($id), 200);
		return response("not found", 404);
	}

	/**
	 * create a DefaultCategory.
	 *
	 * @param DefaultCategoryPostRequest $request
	 * 
	 * 
	 *
	 * @return DefaultCategoryResource
	 */
	public function create(DefaultcategoryPostRequest $request)
	{
		//necessario adicionar aos vcard ja criados?
		$defCat = Defaultcategory::create($request->all());
		return $defCat;
	}


	public function delete($defCat)
	{
		if ($defCat != null) {
			$defCat->forceDelete();
			return response("done", 200);
		}
		return response("Error deleting", 400);
	}

	/**
	 * update a DefaultCategory.
	 *
	 * @param DefaultCategoryPostRequest $request
	 * @param DefaultCategory $defCat
	 * 
	 *
	 * @return DefaultCategoryResource
	 */
	public function update(DefaultcategoryPostRequest $request, DefaultCategory $defCat)
	{
		try {
			$defCat->update($request->all());
			return response(new DefaultcategoryResource($defCat), 200);
		} catch (Exception $e) {
			return response("This id doesnt exist", 401);
		}
	}
}
