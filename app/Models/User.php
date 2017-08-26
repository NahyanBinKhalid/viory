<?php

namespace Viory\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id', 'firstname', 'lastname', 'username', 'email', 'password', 'zipcode', 'dob', 'gender', 'contact', 'is_verified', 'is_social', 'social_type', 'social_token', 'device_token', 'activation_token', 'last_login'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
	    'password', 'is_active', 'is_deleted', 'remember_token',
    ];

	public function country()
	{
		return $this->belongsTo('Viory\Country');
	}

	public function profilePictures()
	{
		return $this->hasMany('Viory\ProfilePicture');
	}
}
