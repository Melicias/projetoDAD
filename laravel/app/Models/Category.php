<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
	use SoftDeletes;
	use HasFactory;
	protected $table = 'categories';
	protected $fillable = ['vcard', 'type', 'name'];

	public function transactions()
	{
		return $this->hasMany(Transaction::class, "category_id");
	}

	public function vcard()
	{
		return $this->belongsTo(Vcard::class,"vcard");
	}

	
}
