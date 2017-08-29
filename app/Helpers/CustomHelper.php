<?php

function generateRandomString($length)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
function createErrorString($errors)
{
	$errors = $errors->toArray();
	$error_response = "";
	foreach($errors as $key_field => $field)
	{
		foreach($field as $key_error => $error)
		{
			$error_response[] = $error;
		}
	}
	return $error_response;
}