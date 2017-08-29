<?php

namespace Viory\Http\Controllers\ApiV1;

use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Viory\Models\User;
use Viory\Http\Controllers\ApiController;

class UsersController extends ApiController
{
	use AuthenticatesUsers;

	public function registerAction( Request $request )
	{
		if($request->has('is_social') && $request->is_social)
		{
			$validator = Validator::make($request->all(), [
				'name'			=>	'max:255',
				'username'		=>	'required|max:255',
				'email'			=>	'required|email|max:255',
				'is_social'		=>	'required|boolean',
				'social_token'	=>	'required'
			]);
		}
		else
		{
			$validator = Validator::make($request->all(), [
				'country_id'    =>  'required|integer',
				'firstname'		=>	'required|max:255',
				'lastname'		=>	'max:255',
				'username'		=>	'required|unique:users|max:255',
				'email'			=>	'required|email|unique:users|max:255',
				'password'		=>	'required|confirmed|min:6',
				'dob'	    	=>	'date',
				'gender'    	=>	'required|in:male,female',
				'contact'    	=>	'required|min:8|max:15'
			]);
		}

		if ($validator->fails())
		{
			$status = [
				'statusCode'		=>	206,
				'httpStatus'		=>	"HTTP_PARTIAL_CONTENT",
				'type'				=>	"validationFailed",
				'message'			=>	createErrorString($validator->messages())
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}

		$validator = Validator::make($request->all(), [
			'email'		=>	'unique:users',
			'username'	=>	'unique:users'
		]);
		if ($validator->fails()) {
			$status = [
				'statusCode'		=>	206,
				'httpStatus'		=>	"HTTP_PARTIAL_CONTENT",
				'type'				=>	"validationFailed",
				'message'			=>	createErrorString($validator->messages()),
			];
			return $this->responseView([], $status, ['Content-Type' => 'application/json'], true);
		}

		try {
			$appUrl = config('app.url');
			if($request->has('is_social') && $request->is_social)
			{
				$user = User::create([
					'name'				=>	$request->get('name'),
					'username'			=>	$request->get('username'),
					'email'				=>	$request->get('email'),
					'password'			=>	bcrypt($request->get('social_token')),
					'is_social'			=>	$request->get('is_social'),
					'is_verified'		=>	true,
					'social_token'		=>	$request->get('social_token')
				]);
				$token = $this->userTokenAction($request->get('email'), $request->get('social_token'));

				$email_view = "emails.registration-social";
				$content = [
					'username'	=>	$user->username,
					'email'		=>	$user->email,
				];
			}
			else
			{
				$user = User::create([
					'name'				=>	$request->get('name'),
					'username'			=>	$request->get('username'),
					'email'				=>	$request->get('email'),
					'password'			=>	bcrypt($request->get('password')),
					'activation_token'	=>	generateRandomString(128)
				]);
				$token = $this->userTokenAction($request->get('email'), $request->get('password'));
				$email_view = "emails.registration";
				$content = [
					'username'			=>	$user->username,
					'email'				=>	$user->email,
					'password'			=>	$request->get('password'),
					'activation_token'	=>	$appUrl . "/v1/verify/" . $user->activation_token,
				];
			}

			Mail::send($email_view, $content, function ($message) use ($user)
			{
				$message->to($user->email)->subject('Welcome to TextPoet');
			});
		} catch (\Exception $exception) {
			$status = [
				'statusCode'		=>	417,
				'httpStatus'		=>	"HTTP_EXPECTATION_FAILED",
				'type'				=>	"expectationFailedException",
				'message'			=>	$exception,
			];
			return $this->responseView([], $status, ['Content-Type' => 'application/json'], true);
		}
		$user = DB::table('users')->where('id', $user->id)->first();
		$user->settings = keyboardSettingsToBinary($user->settings);

		$data = [
			'token'		=>	$token,
			'profile'	=>	$user
		];

		$status = [
			'statusCode'		=>	200,
			'httpStatus'		=>	"HTTP_OK",
			'type'				=>	"userRegisteredSuccessfully",
			'message'			=>	"User registered successfully.",
		];
		return $this->responseView($data, $status, ['Content-Type' => 'application/json'], false);
	}

	protected function userTokenAction($email, $password)
	{
		$client = new Client();
		try {
			$response = $client->post(config('app.url').'/oauth/token', [
				'form_params' => [
					'grant_type' => 'password',
					'client_id' => 4,
					'client_secret' => config('app.client_secret'),
					'username' => $email,
					'password' => $password,
				],
			]);
		} catch (\Exception $exception) {
			return $exception;
		}
		return $token = json_decode((string) $response->getBody(), true);
	}
}
