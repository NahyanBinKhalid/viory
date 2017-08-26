<?php

namespace Viory\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePicture extends Model
{

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id', 'image', 'is_active'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'is_deleted'
	];

	public function user()
	{
		return $this->belongsTo('Viory\User');
	}
}
