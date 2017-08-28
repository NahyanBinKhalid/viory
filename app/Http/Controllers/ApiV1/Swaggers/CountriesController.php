<?php

/**
 * Class CountriesController
 *
 * @package Viory\Http\Controllers\ApiV1
 *
 * Display a listing of the resource.
 *
 * @return \Illuminate\Http\JsonResponse
 *
 * @SWG\Get(
 *     path="/countries",
 *     description="Get Countries List",
 *     summary="Get Countries List.",
 *     operationId="listAction",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     tags={"Countries"},
 *
 *     @SWG\Response(
 *         response=200,
 *         description="HTTP_OK"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="HTTP_NOT_FOUND"
 *     ),
 *     @SWG\Response(
 *         response=500,
 *         description="HTTP_INTERNAL_SERVER_ERROR"
 *     )
 * ),
 *
 * @SWG\Get(
 *     path="/countries/{id}",
 *     description="Get Country Record",
 *     summary="Get Country Record.",
 *     operationId="recordAction",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     tags={"Countries"},
 *
 *     @SWG\Parameter(
 *         description="ID of country to return",
 *         in="path",
 *         name="id",
 *         required=true,
 *         type="integer",
 *         format="int64"
 *     ),
 *
 *     @SWG\Response(
 *         response=200,
 *         description="HTTP_OK"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="HTTP_NOT_FOUND"
 *     ),
 *     @SWG\Response(
 *         response=500,
 *         description="HTTP_INTERNAL_SERVER_ERROR"
 *     )
 * )
 */