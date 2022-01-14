<?php

namespace App\Models;

use App\Models\Transaction;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Vcard extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable;
	use SoftDeletes;
	protected $keyType = 'string';
	protected $primaryKey = 'phone_number';
	protected $fillable = ['phone_number', 'name', 'email', 'photo_url', 'password', 'confirmation_code', 'blocked', 'balance', 'max_debit'];
	protected $table = 'vcards';

	protected $attributes = array(
		'blocked' => false,
		'balance' => 0,
		'max_debit' => 5000
	);

	protected $hidden = [
		'password',
		'confirmation_code',
	];

	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = bcrypt($value);
	}

	public function setConfirmationCodeAttribute($value)
	{
		$this->attributes['confirmation_code'] = bcrypt($value);
	}

	public function transactions()
	{
		return $this->hasMany(Transaction::class, 'vcard', 'phone_number');
	}

	public function categories()
	{
		return $this->hasMany(Category::class, 'vcard', 'phone_number');
	}

	public function piggybank()
	{
		return $this->hasOne(Piggybank::class, 'vcard', 'phone_number')->withTrashed();
	}
}
