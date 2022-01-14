<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Piggybank extends Model
{
	use HasFactory;
	use SoftDeletes;
	protected $table = 'piggybank';
	protected $fillable = ['vcard', 'balance'];

	public function vcard()
	{
		return $this->belongsTo(Vcard::class,"vcard");
	}

	
}
