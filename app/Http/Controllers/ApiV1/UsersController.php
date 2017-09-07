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
				'country_id'    =>  'required|integer',
				'firstname'		=>	'max:255',
				'lastname'		=>	'max:255',
				'username'		=>	'required|unique:users|max:255',
				'email'			=>	'required|email|unique:users|max:255',
				'dob'	    	=>	'date',
				'gender'    	=>	'required|in:male,female',
				'contact'    	=>	'unique:users|min:8|max:15',
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
				'contact'    	=>	'unique:users|min:8|max:15'
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

		try
		{
			if($request->has('is_social') && $request->is_social)
			{
				$user = User::create([
					'country_id'		=>	$request->get('country_id'),
					'firstname'			=>	$request->get('firstname'),
					'lastname'			=>	$request->get('lastname'),
					'username'			=>	$request->get('username'),
					'email'				=>	$request->get('email'),
					'password'			=>	bcrypt($request->get('social_token')),
					'dob'			    =>	$request->get('dob'),
					'gender'		    =>	$request->get('gender'),
					'contact'		    =>	$request->get('contact'),
					'is_verified'		=>	true,
					'is_social'			=>	$request->get('is_social'),
					'social_token'		=>	$request->get('social_token')
				]);
				$token = $this->userTokenAction($request->get('email'), $request->get('social_token'));

				$email_view = "emails.registration-social";
				$content = [
					'firstname'	=>	$user->firstname,
					'lastname'	=>	$user->lastname,
					'username'	=>	$user->username,
					'email'		=>	$user->email,
				];
			}
			else
			{
				$user = User::create([
					'country_id'		=>	$request->get('country_id'),
					'firstname'			=>	$request->get('firstname'),
					'lastname'			=>	$request->get('lastname'),
					'username'			=>	$request->get('username'),
					'email'				=>	$request->get('email'),
					'password'			=>	bcrypt($request->get('password')),
					'dob'		    	=>	$request->get('dob'),
					'gender'			=>	$request->get('gender'),
					'contact'			=>	$request->get('contact'),
					'activation_token'	=>	generateRandomString(128)
				]);
				$token = $this->userTokenAction($request->get('email'), $request->get('password'));
				$email_view = "emails.registration";
				$content = [
					'firstname'			=>	$user->firstname,
					'lastname'			=>	$user->lastname,
					'username'			=>	$user->username,
					'email'				=>	$user->email,
					'password'			=>	$request->get('password'),
					'activation_token'	=>	config('app.url') . "/api/v1/verify/" . $user->activation_token,
				];
			}

//			Mail::send($email_view, $content, function ($message) use ($user)
//			{
//				$message->to($user->email)->subject('Welcome to Viory');
//			});

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
		catch (\Exception $exception)
		{
			$status = [
				'statusCode'		=>	417,
				'httpStatus'		=>	"HTTP_EXPECTATION_FAILED",
				'type'				=>	"expectationFailedException",
				'message'			=>	$exception->getMessage()
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}
	}

	public function loginAction(Request $request)
	{
		if($request->has('is_social') && $request->is_social)
		{
			$validator = Validator::make($request->all(), [
				'social_token'		=>	'required',
				'is_social'			=>	'required|boolean'
			]);
		}
		else
		{
			$validator = Validator::make($request->all(), [
				'identity'		=>	'required',
				'password'		=>	'required'
			]);
		}
		if ($validator->fails()) {
			$status = [
				'statusCode'		=>	206,
				'httpStatus'		=>	"HTTP_PARTIAL_CONTENT",
				'type'				=>	"validationFailed",
				'message'			=>	createErrorString($validator->messages())
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}

		if($request->has('is_social') && $request->is_social)
		{
			$user = User::where('is_social', $request->get('is_social'))->where('social_token', $request->get('social_token'))->where('is_deleted', false)->first();
			$password = $request->get('social_token');
		}
		else
		{
			$user = User::where('email', $request->get('identity'))->orWhere('username', $request->get('identity'))->where('is_deleted', false)->first();
			$password = $request->get('password');
		}

		if(!$user || !Hash::check($password, $user->password))
		{
			$status = [
				'statusCode'		=>	203,
				'httpStatus'		=>	"HTTP_NON_AUTHORITATIVE_INFORMATION",
				'type'				=>	"validationFailed",
				'message'			=>	"These credentials do not match our record."
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}

		$token = $this->userTokenAction($user->email, $password);

//		if(!$token)
//		{
//			return $this->responseView([], $this->getFailedLogin(), ['Content-Type' => 'application/json'], true);
//		}

		$user = DB::table('users')->where('id', $user->id)->first();
		$nowTime = Carbon::now();
		DB::table('users')->where('id', $user->id)->update(['last_login' => $nowTime->toDateTimeString()]);

		$data = [
			'token'		=>	$token,
			'profile'	=>	$user
		];

		$status = [
			'statusCode'		=>	200,
			'httpStatus'		=>	"HTTP_OK",
			'type'				=>	"userLoginSuccessful",
			'message'			=>	"User logged in successfully.",
		];
		return $this->responseView($data, $status, ['Content-Type' => 'application/json'], false);
	}

	public function validatePasswordAction(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'password' => 'required'
		]);

		if ($validator->fails()) {
			$status = [
				'statusCode'		=>	206,
				'httpStatus'		=>	"HTTP_PARTIAL_CONTENT",
				'type'				=>	"validationFailed",
				'message'			=>	createErrorString($validator->messages())
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}

		$user = Auth::User();

		if(Hash::check($request->get('password'), $user->password)) {
			$status = [
				'statusCode'		=>	200,
				'httpStatus'		=>	"HTTP_OK",
				'type'				=>	"passwordCorrect",
				'message'			=>	"Password correct.",
			];
			return $this->notificationView($status, ['Content-Type' => 'application/json'], false);
		} else {
			$status = [
				'statusCode'		=>	203,
				'httpStatus'		=>	"HTTP_NON_AUTHORITATIVE_INFORMATION",
				'type'				=>	"passwordIncorrect",
				'message'			=>	"You have entered incorrect password.",
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}
	}

	public function validateEmailAction(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email'	=>	'required|email|max:255',
		]);

		if ($validator->fails()) {
			$status = [
				'statusCode'		=>	206,
				'httpStatus'		=>	"HTTP_PARTIAL_CONTENT",
				'type'				=>	"validationFailed",
				'message'			=>	createErrorString($validator->messages())
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}

		$user = User::where('email', $request->email)->first();

		if($user)
		{
			$status = [
				'statusCode'		=>	200,
				'httpStatus'		=>	"HTTP_OK",
				'type'				=>	"emailExist",
				'message'			=>	"Email Already Exists.",
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}
		else
		{
			$status = [
				'statusCode'		=>	200,
				'httpStatus'		=>	"HTTP_OK",
				'type'				=>	"emailAvailable",
				'message'			=>	"Email available.",
			];
			return $this->notificationView($status, ['Content-Type' => 'application/json'], false);
		}
	}

	public function validateUsernameAction(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'username'	=>	'required|max:255'
		]);

		if ($validator->fails()) {
			$status = [
				'statusCode'		=>	206,
				'httpStatus'		=>	"HTTP_PARTIAL_CONTENT",
				'type'				=>	"validationFailed",
				'message'			=>	createErrorString($validator->messages())
			];
			return $this->responseView([], $status, ['Content-Type' => 'application/json'], true);
		}

		$user = User::where('username', $request->username)->first();

		if($user)
		{
			$status = [
				'statusCode'		=>	200,
				'httpStatus'		=>	"HTTP_OK",
				'type'				=>	"usernameExist",
				'message'			=>	"Username Already Exists.",
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}
		else
		{
			$status = [
				'statusCode'		=>	200,
				'httpStatus'		=>	"HTTP_OK",
				'type'				=>	"usernameAvailable",
				'message'			=>	"Username available.",
			];
			return $this->notificationView($status, ['Content-Type' => 'application/json'], false);
		}
	}

	public function changePasswordAction(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'old_password'			=>	'required',
			'new_password'			=>	'required',
			'new_confirm_password'	=>	'required|same:new_password',
		]);

		if ($validator->fails()) {
			$status = [
				'statusCode'		=>	206,
				'httpStatus'		=>	"HTTP_PARTIAL_CONTENT",
				'type'				=>	"validationFailed",
				'message'			=>	createErrorString($validator->messages())
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}

		$user = Auth::User();

		if($user->is_social)
		{
			$status = [
				'statusCode'		=>	200,
				'httpStatus'		=>	"HTTP_OK",
				'type'				=>	"usingSocialCredentials",
				'message'			=>	"It looks like you're using your Social Credentials to Login. To change your password go to your Social Account settings page.",
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}

		if ($request->get("old_password") == $request->get("new_password"))
		{
			$status = [
				'statusCode'		=>	200,
				'httpStatus'		=>	"HTTP_OK",
				'type'				=>	"sameOldandNewPassword",
				'message'			=>	"Current password and new password shouldn\'t match.",
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}

		if (!Hash::check($request->get("old_password"), $user->password))
		{
			$status = [
				'statusCode'		=>	200,
				'httpStatus'		=>	"HTTP_OK",
				'type'				=>	"invalidPassword",
				'message'			=>	"You have entered incorrect current password.",
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}
		else
		{
			User::where('id', $user->id)->update(['password' => bcrypt($request->get('new_password'))]);
			$status = [
				'statusCode'		=>	200,
				'httpStatus'		=>	"HTTP_OK",
				'type'				=>	"passwordChangedSuccessfully",
				'message'			=>	"Password changed successfully.",
			];
			return $this->notificationView($status, ['Content-Type' => 'application/json'], false);
		}
	}

	public function forgotPasswordAction(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email'			=>	'required|email'
		]);
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

		$user = User::where('email', $request->get('email'))->where('is_deleted', false)->first();


		if(!$user)
		{
			$status = [
				'statusCode'		=>	204,
				'httpStatus'		=>	"HTTP_NO_CONTENT",
				'type'				=>	"noEmailFound",
				'message'			=>	"No account found with that Email."
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}

		if($user->is_social)
		{
			$status = [
				'statusCode'		=>	200,
				'httpStatus'		=>	"HTTP_OK",
				'type'				=>	"usingSocialCredentials",
				'message'			=>	"It looks like you're using your Social Credentials to Login. To change your password go to your Social Account settings page.",
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}

		$forgot_token = generateRandomString(128);
		User::where('id', $user->id)->update(['forgot_token' => $forgot_token]);
		$content = [
			'firstname'		=>	$user->firstname,
			'lastname'		=>	$user->lastname,
			'username'		=>	$user->username,
			'email'			=>	$user->email,
			'forgot_link'	=>	"viory://" . $forgot_token,
		];
//		Mail::send('emails.forgot-password', $content, function ($message) use ($user)
//		{
//			$message->to($user->email)->subject('TextPoet Password Reset');
//		});
		$status = [
			'statusCode'		=>	200,
			'httpStatus'		=>	"HTTP_OK",
			'type'				=>	"PasswordResetSuccessful",
			'message'			=>	"Password reset successfully.",
		];
		return $this->notificationView($status, ['Content-Type' => 'application/json'], false);

	}

	public function resetPasswordAction(Request $request, $token)
	{
		$validator = Validator::make($request->all(), [
			'new_password'			=>	'required',
			'new_confirm_password'	=>	'required|same:new_password'
		]);
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

		$user = User::where('forgot_token', $token)->first();

		if(!$user)
		{
			$status = [
				'statusCode'		=>	204,
				'httpStatus'		=>	"HTTP_NO_CONTENT",
				'type'				=>	"invalidToken",
				'message'			=>	"Invalid Token or Not Found."
			];
			return $this->errorView($status, ['Content-Type' => 'application/json']);
		}

		User::where('id', $user->id)->update(['password' => bcrypt($request->get('new_password')), 'forgot_token' => null]);
		$status = [
			'statusCode'		=>	200,
			'httpStatus'		=>	"HTTP_OK",
			'type'				=>	"passwordChangedSuccessfully",
			'message'			=>	"Password changed successfully.",
		];
		return $this->notificationView($status, ['Content-Type' => 'application/json'], false);
	}

	public function logoutAction(Request $request)
	{
		$request->user()->token()->revoke();
		$status = [
			'statusCode'		=>	200,
			'httpStatus'		=>	"HTTP_OK",
			'type'				=>	"userLogoutSuccessful",
			'message'			=>	"User logged out successfully.",
		];
		return $this->notificationView($status, ['Content-Type' => 'application/json'], false);
	}

	public function deleteProfileAction(Request $request)
	{
		$user = $request->user();
		if( !$user->is_social )
		{
			$validator = Validator::make($request->all(), [
				'password'		=>	'required|max:255'
			]);
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

			if(!Hash::check($request->get('password'), $user->password))
			{
				$status = [
					'statusCode'		=>	200,
					'httpStatus'		=>	"HTTP_OK",
					'type'				=>	"invalidPassword",
					'message'			=>	"You have entered incorrect current password.",
				];
				return $this->errorView($status, ['Content-Type' => 'application/json']);
			}
		}
		else
		{
			$validator = Validator::make($request->all(), [
				'social_token'		=>	'required|max:255'
			]);
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

			if($request->get('social_token') != $user->social_token)
			{
				$status = [
					'statusCode'		=>	204,
					'httpStatus'		=>	"HTTP_NO_CONTENT",
					'type'				=>	"invalidSocialToken",
					'message'			=>	"Invalid Social Token or Not Found."
				];
				return $this->errorView($status, ['Content-Type' => 'application/json']);
			}
		}

//		if(!$user->is_verified)
//		{
//			$status = [
//				'statusCode'		=>	204,
//				'httpStatus'		=>	"HTTP_NO_CONTENT",
//				'type'				=>	"unverifiedEmail",
//				'message'			=>	"User Email not Verified."
//			];
//			return $this->errorView($status, ['Content-Type' => 'application/json']);
//		}

		User::where('id', $user->id)->update(['is_deleted' => true]);
		$request->user()->token()->revoke();
		$status = [
			'statusCode'		=>	200,
			'httpStatus'		=>	"HTTP_OK",
			'type'				=>	"userDeleteSuccessful",
			'message'			=>	"User deleted successfully.",
		];
		return $this->notificationView($status, ['Content-Type' => 'application/json'], false);
	}

	public function verifyEmailAction($token)
	{
		$user = User::where('activation_token', $token)->first();
		if(!$user)
		{
			$data = [
				'username'	=> $user->username,
				'message'	=> "InValid Token"
			];
		}
		else
		{
			if($user->is_verified)
			{
				$data = [
					'username'	=> $user->username,
					'message'	=> "Email Already Activated"
				];
			}
			else
			{
				User::where('id', $user->id)->update(['is_verified' => true]);
				$data = [
					'username'	=> $user->username,
					'message'	=> "Email activated successfully."
				];
			}
		}

		return view('pages.verify', $data);
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
			return $exception->getMessage();
		}
		return $token = json_decode((string) $response->getBody(), true);
	}
}
