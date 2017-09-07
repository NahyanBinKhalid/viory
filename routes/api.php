<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(["prefix" => "v1"], function () {

	Route::get("/countries", "ApiV1\CountriesController@listAction");
	Route::get("/countries/{any}", "ApiV1\CountriesController@recordAction");

	Route::post("/register", "ApiV1\UsersController@registerAction");
	Route::post("/login", "ApiV1\UsersController@loginAction");
	Route::post("/validate-email", "ApiV1\UsersController@validateEmailAction");
	Route::post("/validate-username", "ApiV1\UsersController@validateUsernameAction");
	Route::post("/forgot-password", "ApiV1\UsersController@forgotPasswordAction");
	Route::post("/reset-password/{token}", "ApiV1\UsersController@resetPasswordAction");

	Route::group(['middleware' => 'auth:api'], function () {
		Route::post("/validate-password", "ApiV1\UsersController@validatePasswordAction");
		Route::post("/change-password", "ApiV1\UsersController@changePasswordAction");
		Route::get("/logout", "ApiV1\UsersController@logoutAction");
		Route::delete("/profile/delete", "ApiV1\UsersController@deleteProfileAction");
	});

//	Route::middleware('auth:api')->get('/user', function (Request $request) {
//		return $request->user();
//	});
});

