<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\CategoryPostRequest;
use App\Models\Transaction;

class CategoryService
{

	/**
	 * create a Category.
	 *
	 * @param CategoryPostRequest $request
	 * 
	 *
	 * @return CategoryResource
	 */
	public function create(CategoryPostRequest $request)
	{
		$category = Category::create($request->all());
		return new CategoryResource($category);
	}

	/**
	 * update a Category.
	 *
	 * @param CategoryPostRequest $request
	 * @param number $id
	 * 
	 *
	 * @return CategoryResource
	 */
	public function update(CategoryPostRequest $request, $category)
	{
		if ($request->get('type')) {
			if ($request->get('type') != $category->type) {
				$thereIsTransactions = Transaction::where('category_id', $category->id)->count();
				if ($thereIsTransactions > 0) {
					return response("There is already transaction with this category so you can't change the type", 400);
				}
			}
		}
		if ($request->get('type') && $request->get('name')) {
			$exist = Category::where('type', $request->get('type'))->where('name', $request->get('name'))->where('vcard', $request->get('vcard'))->where('id', '<>', $category->id)->count();
		} else {
			if ($request->get('type')) {
				$exist = Category::where('type', $category->type)->where('name', $request->get('name'))->where('vcard', $request->get('vcard'))->where('id', '<>', $category->id)->count();
			} else {
				$exist = Category::where('type', $category->type)->where('name', $category->name)->where('vcard', $request->get('vcard'))->where('id', '<>', $category->id)->count();
			}
		}
		if ($exist > 0) {
			return response("This Category already exists in your profile!", 400);
		}
		$category->update($request->all());
		return response(new CategoryResource($category), 201);;
	}

	/**
	 * delete a Category.
	 *
	 * @param number $id
	 * 
	 *
	 * @return CategoryResource
	 */
	public function delete($category)
	{
		$def = Category::withTrashed()->find($category);
		if ($def != null) {
			if ($def->deleted_at == null) {
				if (count($def->transactions) > 0) {
					$def->delete();
				} else {
					$def->forceDelete();
				}
			} else {
				$def->deleted_at = null;
				$def->save();
			}
		}
	}
}
