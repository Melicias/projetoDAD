<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Defaultcategory extends Model
{

	use SoftDeletes;
    use HasFactory;
	protected $table = 'default_categories';
	protected $fillable = ['type','name'];
}
