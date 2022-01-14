<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentType extends Model
{
	use HasFactory;
	use SoftDeletes;
	protected $table = 'payment_types';
	protected $keyType = 'string';
	protected $primaryKey = 'code';
	protected $fillable = ['code', 'name', 'description', 'validation_rules', 'custom_options'];

	public function transactions()
	{
		return $this->hasMany(Transaction::class);
	}
}
