<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Lang;

trait ErrorResponse
{

	/**
	 * Get the invalid parameter message.
	 *
	 * @return array
	 */
	protected function getInvalidParameter()
	{
		return [
			'statusCode'	=>	'400',
			'httpStatus'	=>	'HTTP_BAD_REQUEST',
			'type'			=>	Lang::has('errors.type.invalid') ? Lang::get('errors.type.invalid') : 'invalidParameter',
			'message'		=>	Lang::has('errors.description.invalid') ? Lang::get('errors.description.invalid') : 'A required parameter is missing or invalid in the request.'
		];
	}
	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getForbidden()
	{
		return [
			'statusCode'	=>	'400',
			'httpStatus'	=>	'HTTP_FORBIDDEN',
			'type'			=>	Lang::has('errors.type.forbidden') ? Lang::get('errors.type.forbidden') : 'forbidden',
			'message'		=>	Lang::has('errors.description.forbidden') ? Lang::get('errors.description.forbidden') : 'Access forbidden. The request may not be properly authorized.'
		];
	}
	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getUsernameEmailAlreadyExist()
	{
		return [
			'statusCode'	=>	'200',
			'httpStatus'	=>	'HTTP_OK',
			'type'			=>	Lang::has('errors.type.conflict') ? Lang::get('errors.type.conflict') : 'conflict',
			'message'		=>	Lang::has('errors.description.conflict') ? Lang::get('errors.description.conflict') : 'An account with this email or username already exists.'
		];
	}
	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getUserEmailAlreadyExist()
	{
		return [
			'statusCode'	=>	'200',
			'httpStatus'	=>	'HTTP_OK',
			'type'			=>	Lang::has('errors.type.conflict') ? Lang::get('errors.type.conflict') : 'conflict',
			'message'		=>	Lang::has('errors.description.conflict') ? Lang::get('errors.description.conflict') : 'An account with this email already exists.'
		];
	}
	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getUserUsernameAlreadyExist()
	{
		return [
			'statusCode'	=>	'200',
			'httpStatus'	=>	'HTTP_OK',
			'type'			=>	Lang::has('errors.type.conflict') ? Lang::get('errors.type.conflict') : 'conflict',
			'message'		=>	Lang::has('errors.description.conflict') ? Lang::get('errors.description.conflict') : 'An account with this username already exists.'
		];
	}
	/**
	 * Get the not found message.
	 *
	 * @return array
	 */
	protected function getNotFound()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.notfound') ? Lang::get('errors.type.notfound') : 'recordNotFound',
			'message'		=>	Lang::has('errors.description.notfound') ? Lang::get('errors.description.notfound') : 'Record Not found.'
		];
	}
	/**
	 * Get the not found message.
	 *
	 * @return array
	 */
	protected function getNoUserEmailExist()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.notfound') ? Lang::get('errors.type.notfound') : 'emailNotFound',
			'message'		=>	Lang::has('errors.description.notfound') ? Lang::get('errors.description.notfound') : 'No account found with that Email.'
		];
	}
	/**
	 * Get the fail Login message.
	 *
	 * @return array
	 */
	protected function getFailedLogin()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.failLogin') ? Lang::get('errors.type.failLogin') : 'accountNotFound',
			'message'		=>	Lang::has('errors.description.failLogin') ? Lang::get('errors.description.failLogin') : 'You have entered invalid credentials.'
		];
	}

	/**
	 * Get the fail Login message.
	 *
	 * @return array
	 */
	protected function getInvalidCurrentPassword()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.forbidden') ? Lang::get('errors.type.forbidden') : 'forbidden',
			'message'		=>	Lang::has('errors.description.invalidPassword') ? Lang::get('errors.description.invalidPassword') : 'You have entered incorrect current password.'
		];
	}

	/**
	 * Get the fail Login message.
	 *
	 * @return array
	 */
	protected function getInvalidPassword()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.forbidden') ? Lang::get('errors.type.forbidden') : 'forbidden',
			'message'		=>	Lang::has('errors.description.invalidPassword') ? Lang::get('errors.description.invalidPassword') : 'You have entered incorrect password.'
		];
	}

	/**
	 * Get the fail Login message.
	 *
	 * @return array
	 */
	protected function getSamePassword()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.forbidden') ? Lang::get('errors.type.forbidden') : 'forbidden',
			'message'		=>	Lang::has('errors.description.invalidPassword') ? Lang::get('errors.description.invalidPassword') : 'Current password and new password shouldn\'t match.'
		];
	}
	/**
	 * Get the fail Login message.
	 *
	 * @return array
	 */
	protected function getInternelServerError()
	{
		return [
			'statusCode'	=>	'500',
			'httpStatus'	=>	'HTTP_INTERNAL_SERVER_ERROR',
			'type'			=>	Lang::has('errors.type.serverError') ? Lang::get('errors.type.serverError') : 'internalServerError',
			'message'		=>	Lang::has('errors.description.serverError') ? Lang::get('errors.description.serverError') : 'The server encountered an internal error or misconfiguration and was unable to complete your request.'
		];
	}

	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getTokenNotFound()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.tokenNotFound') ? Lang::get('errors.type.tokenNotFound') : 'tokenNotFound',
			'message'		=>	Lang::has('errors.description.tokenNotFound') ? Lang::get('errors.description.tokenNotFound') : 'Token not found.'
		];
	}

	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getSocialInvalidToken()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.tokenNotFound') ? Lang::get('errors.type.tokenNotFound') : 'socialTokenInvalid',
			'message'		=>	Lang::has('errors.description.tokenNotFound') ? Lang::get('errors.description.tokenNotFound') : 'Social Token Invalid.'
		];
	}
	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getInvalidRequest()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.invalidRequest') ? Lang::get('errors.type.invalidRequest') : 'invalidRequest',
			'message'		=>	Lang::has('errors.description.invalidRequest') ? Lang::get('errors.description.invalidRequest') : 'Invalid request.'
		];
	}
	/**
	 * Get the fail Login message.
	 *
	 * @return array
	 */
	protected function getFailedRecoverPassword()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.failLogin') ? Lang::get('errors.type.failLogin') : 'accountNotFound',
			'message'		=>	Lang::has('errors.description.failLogin') ? Lang::get('errors.description.failLogin') : 'No account found with this email.'
		];
	}
	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getFacebookRecoverPassword()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.invalidRequest') ? Lang::get('errors.type.invalidRequest') : 'invalidRequest',
			'message'		=>	Lang::has('errors.description.invalidRequest') ? Lang::get('errors.description.invalidRequest') : "It looks like you're using your Facebook Credentials to Login. To change your Facebook password go to your Facebook settings page."
		];
	}
	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getFacebookEditEmail()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.invalidRequest') ? Lang::get('errors.type.invalidRequest') : 'invalidRequest',
			'message'		=>	Lang::has('errors.description.invalidRequest') ? Lang::get('errors.description.invalidRequest') : "Cannot Change Social Email on TextPoet."
		];
	}
	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getLinkedinRecoverPassword()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.invalidRequest') ? Lang::get('errors.type.invalidRequest') : 'invalidRequest',
			'message'		=>	Lang::has('errors.description.invalidRequest') ? Lang::get('errors.description.invalidRequest') : "It looks like you're using your LinkedIn credentials to log in to TextPoet. To change your LinkedIn password go to your LinkedIn settings page."
		];
	}
	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getInvalidFileType()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.invalidRequest') ? Lang::get('errors.type.invalidRequest') : 'badRequest',
			'message'		=>	Lang::has('errors.description.invalidRequest') ? Lang::get('errors.description.invalidRequest') : "Uploading invalid File type."
		];
	}
	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getUserUnverified()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.invalidRequest') ? Lang::get('errors.type.invalidRequest') : 'preconditionFailed',
			'message'		=>	Lang::has('errors.description.invalidRequest') ? Lang::get('errors.description.invalidRequest') : "User Email not Verified."
		];
	}
	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getUserAlreadyActivated()
	{
		return [
			'statusCode'	=>	'203',
			'httpStatus'	=>	'HTTP_NON_AUTHORITATIVE_INFORMATION',
			'type'			=>	Lang::has('errors.type.invalidRequest') ? Lang::get('errors.type.invalidRequest') : 'preconditionFailed',
			'message'		=>	Lang::has('errors.description.invalidRequest') ? Lang::get('errors.description.invalidRequest') : "User Email already Verified."
		];
	}
	/**
	 * Get the forbidden message.
	 *
	 * @return array
	 */
	protected function getGroupAlreadyExist()
	{
		return [
			'statusCode'	=>	'200',
			'httpStatus'	=>	'HTTP_OK',
			'type'			=>	Lang::has('errors.type.conflict') ? Lang::get('errors.type.conflict') : 'conflict',
			'message'		=>	Lang::has('errors.description.conflict') ? Lang::get('errors.description.conflict') : 'Group already exists.'
		];
	}
}