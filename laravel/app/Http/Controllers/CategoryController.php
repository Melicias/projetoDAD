<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Requests\CategoryPostRequest;
use App\Models\Category;

class CategoryController extends Controller
{

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function store(CategoryPostRequest $request)
	{
		$category = (new CategoryService)->create($request);
		return response($category, 200);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  Category  $category
	 * @return \Illuminate\Http\Response
	 */
	public function update(CategoryPostRequest $request, Category $category)
	{
		if ($request->vcard != Request()->user()->phone_number) {
			return response("unauthorized", 401);
		}
		return (new CategoryService)->update($request, $category);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{

		(new CategoryService)->delete($id);
		return response(200);
	}
}
