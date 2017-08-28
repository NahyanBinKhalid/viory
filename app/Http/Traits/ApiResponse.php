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
		if(sizeof($data) > 0)
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

	protected function view($data = null, $statusCode = null, array $headers = array())
	{
		return response()->json($data,$statusCode,$headers);
	}
	/**
	 * Creates a View instance for the given data.
	 *
	 * @param mixed $data [optional]
	 * @param array $metaData [optional]
	 * @param int   $statusCode [optional]
	 *
	 * @return Response
	 */
	protected function successView($data = null, array $metaData = array(), $statusCode = self::HTTP_OK)
	{
		$payload = null;
		// do only create a payload, if there is any response data given. this enables us to send responses without
		// a body, which is required for some HTTP status (like 204).
		if (!is_null($data)) {
			$data = array(
				'data' => $data,
			);
			$payload = array_merge($metaData, $data);
		}
		return $this->view($payload, $statusCode);
	}
	/**
	 * Creates a Response instance signaling an error described by the given message.
	 *
	 * @param mixed $errorMessage
	 * @param array $metaData [optional]
	 * @param int   $statusCode [optional]
	 *
	 * @return Response
	 */
	protected function errorView($errorMessage, array $metaData = array(), $statusCode = self::HTTP_INTERNAL_SERVER_ERROR)
	{
		$data = ['error'	=>	$errorMessage];
		$payload = array_merge($metaData, $data);
		return $this->view($payload, $statusCode);
	}
	/**
	 * Creates a Response when status passed in result.
	 *
	 * @param array $result
	 * @param boolean   $paginated [optional]
	 *
	 * @return Response
	 */
	public function returnView($result=array(), $paginated=false)
	{
		if(!isset($result['status']))
			if($paginated)
				return $this->paginatedListView($result);
			else
				return $this->successView($result);
		if($result['status']==self::HTTP_OK)
			if($paginated)
				return $this->paginatedListView($result['data']);
			else
				return $this->successView($result['data'],array() , $result['status']);
		elseif($result['status']==self::HTTP_NO_CONTENT)
			return  $this->handleBooleanView(true);
		else
			return $this->errorView($result['data'],array() , $result['status']);
	}
	protected function handleBooleanView($successful=true) {
		return response()->make('',$successful ? 204 : 400);
	}
	/**
	 * Creates a view based on the given list, containing additional pagination information.
	 *
	 * @param LengthAwarePaginator $list
	 * @param array $meta [optional]
	 * @return Response
	 */
	protected function paginatedListView(LengthAwarePaginator $list, array $meta=array() )
	{
		$meta = array_merge(
			$meta,
			array(
				'paging' => array(
					'total'  => $list->total(),
					'per_page'    => $list->perPage(),
					'current_page'  => $list->currentPage(),
					'last_page'  => $list->lastPage(),
					'next_page_url'  => $list->nextPageUrl(),
					'prev_page_url'  => $list->previousPageUrl(),
					'from'  => $list->firstItem(),
					'to'  => $list->lastItem(),
					'response_ts'  => time(),
					// backward-compatibility fields
					'count'        => $list->total(),
					'pages'        => $list->lastPage(),
					'current'      => $list->currentPage(),
				)
			)
		);
		return $this->successView(iterator_to_array($list), $meta);
	}
	/****************************
	 * STATUS CODE FUNCTIONS
	 ****************************/
	protected function return404Unless($condition, $message=null) {
		$this->returnHttpStatusUnless($condition, 404, $message);
	}
	protected function return403Unless($condition, $message=null) {
		$this->returnHttpStatusUnless($condition, 403, $message);
	}
	protected function return401Unless($condition, $message=null) {
		$this->returnHttpStatusUnless($condition, 401, $message);
	}
	protected function return400Unless($condition, $message=null) {
		$this->returnHttpStatusUnless($condition, 400, $message);
	}
	protected function return404If($condition, $message=null) {
		$this->returnHttpStatusIf($condition, 404, $message);
	}
	protected function return403If($condition, $message=null) {
		$this->returnHttpStatusIf($condition, 403, $message);
	}
	protected function return400If($condition, $message=null) {
		$this->returnHttpStatusIf($condition, 400, $message);
	}
	protected function return401If($condition, $message=null) {
		$this->returnHttpStatusIf($condition, 401, $message);
	}
	protected function returnHttpStatusUnless($condition, $status, $message=null) {
		if ( !$condition ) {
			throw new HttpException($status, $message);
		}
	}
	protected function returnHttpStatusIf($condition, $status, $message=null) {
		if ( $condition ) {
			throw new HttpException($status, $message);
		}
	}
}