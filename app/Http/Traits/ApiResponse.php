<?php

namespace Viory\Http\Traits;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Lang;

trait ApiResponse
{
	/**
	 * Create a view
	 *
	 * Convenience method to allow for a fluent interface.
	 *
	 * @param mixed   $data
	 * @param integer $statusCode
	 * @param array   $headers
	 *
	 * @return Response
	 */
	protected function responseView($data = [], $status = [], $headers = [], $error = false) {
		//		$status = "statusCode, httpStatus, type, message";
		if(sizeof($data) > 0 && !$error)
		{
			$responseData = [
				"error"			=>	$error,
				"status_code"	=>	$status['statusCode'],
				"http_status"	=>	$status['httpStatus'],
				"type"			=>	$status['type'],
				"message"		=>	$status['message'],
				"data"			=>	$data
			];
		}
		else
		{
			$responseData = [
				"error"			=>	true,
				"status_code"	=>	404,
				"http_status"	=>	"HTTP_NOT_FOUND",
				"type"			=>	"notFound",
				"message"		=>	"Record Not Found",
				"data"			=>	[]
			];
		}
		return response()->json($responseData, $status['statusCode'], $headers);
	}

	protected function notificationView($status = [], $headers = [], $error = false) {
		//		$status = "statusCode, httpStatus, type, message";

		$responseData = [
			"error"			=>	$error,
			"status_code"	=>	$status['statusCode'],
			"http_status"	=>	$status['httpStatus'],
			"type"			=>	$status['type'],
			"message"		=>	$status['message'],
			"data"			=>	[]
		];
		return response()->json($responseData, $status['statusCode'], $headers);
	}

	protected function errorView($status = [], $headers = []) {
		//		$status = "statusCode, httpStatus, type, message";
		$responseData = [
			"error"			=>	true,
			"status_code"	=>	$status['statusCode'],
			"http_status"	=>	$status['httpStatus'],
			"type"			=>	$status['type'],
			"message"		=>	$status['message'],
			"data"			=>	[]
		];

		return response()->json($responseData, $status['statusCode'], $headers);
	}
}