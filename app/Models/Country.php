<?php

namespace Viory\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
		'country', 'iso_code_2', 'iso_code_3'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [];

	public function users()
	{
		return $this->hasMany('Viory\User');
	}
}

