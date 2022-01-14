<?php

namespace App\Models;

use App\Models\Vcard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
	use HasFactory;
	use SoftDeletes;
	protected $fillable = [
		'vcard', 'date', 'datetime', 'type', 'value', 'old_balance', 'new_balance',
		'payment_type', 'payment_reference', 'pair_transaction', 'pair_vcard', 'category_id', 'description'
	];

	public function Vcard()
	{
		return $this->belongsTo(Vcard::class, "vcard")->withTrashed();
	}

	public function Category()
	{
		return $this->belongsTo(Category::class, "category_id")->withTrashed();
	}

	public function PairTransaction()
	{
		return $this->belongsTo(Transaction::class, 'pair_transaction');
	}

	public function PairVcard()
	{
		return $this->belongsTo(Vcard::class, "pair_vcard");
	}

	public function PaymentType()
	{
		return $this->belongsTo(PaymentType::class, "payment_type");
	}

	public function scopeSearch($query, $type)
	{
		return $query->where('vcard', 'like', '%' . $type . '%')
			->orWhere('pair_transaction', 'like', '%' . $type . '%')
			->orWhere('type', 'like', '%' . $type . '%')
			->orWhere('payment_type', 'like', '%' . $type . '%');
	}

	public function scopeDate($query, $vcard, $month)
	{
		//return $query->where('vcard', 'like', $vcard)->whereDate('created_at', '>=', today()->startOfWeek()->subWeeks($type));
		return $query->where('vcard', 'like', $vcard)->whereYear('created_at', date("Y"))->whereMonth('created_at', $month);
	}
}
