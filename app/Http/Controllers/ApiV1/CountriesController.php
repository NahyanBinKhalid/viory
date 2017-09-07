<?php

namespace Viory\Http\Controllers\ApiV1;

use Illuminate\Http\Request;
use Viory\Http\Controllers\ApiController;
use Viory\Models\Country;

class CountriesController extends ApiController
{
	public function listAction(Request $request)
	{
		$countries = Country::all();
		$status = [
			'statusCode'		=>	200,
			'httpStatus'		=>	"HTTP_OK",
			'type'				=>	"countriesListFetched",
			'message'			=>	"Countries List Fetched."
		];
		return $this->responseView($countries, $status, ['Content-Type' => 'application/json'], false);
	}

	public function recordAction(Request $request, $any)
	{
		$country = Country::where('id', $any)->orWhere('country', $any)->first();
		$status = [
			'statusCode'		=>	200,
			'httpStatus'		=>	"HTTP_OK",
			'type'				=>	"countryFetched",
			'message'			=>	"Country Fetched."
		];
		return $this->responseView($country, $status, ['Content-Type' => 'application/json'], false);
	}
}
