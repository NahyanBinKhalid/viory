<?php
/**
 * Class UsersController
 *
 * @package Viory\Http\Controllers\ApiV1
 *
 * Display a listing of the resource.
 *
 * @return \Illuminate\Http\JsonResponse
 *
 *
 * @SWG\Post(
 *     path="/register",
 *     description="Registers a User.",
 *     summary="Registers a User.",
 *     operationId="registerAction",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     tags={"Users"},
 *     @SWG\Parameter(
 *         description="Full Name of a User",
 *         in="formData",
 *         name="name",
 *         required=false,
 *         type="string",
 *         format="string"
 *     ),
 *     @SWG\Parameter(
 *         description="Unique username",
 *         in="formData",
 *         name="username",
 *         required=true,
 *         type="string",
 *         format="string"
 *     ),
 *     @SWG\Parameter(
 *         description="Unique User Email",
 *         in="formData",
 *         name="email",
 *         required=true,
 *         type="string",
 *         format="email"
 *     ),
 *     @SWG\Parameter(
 *         description="Password",
 *         in="formData",
 *         name="password",
 *         required=false,
 *         type="string",
 *         format="string"
 *     ),
 *     @SWG\Parameter(
 *         description="Social Account [0: Normal Registeration | 1: Facebook Login]",
 *         in="formData",
 *         name="is_social",
 *         required=false,
 *         type="boolean",
 *         format="int32",
 *		  default=false
 *     ),
 *
 *     @SWG\Response(
 *         response=200,
 *         description="HTTP_OK"
 *     ),
 *     @SWG\Response(
 *         response=401,
 *         description="HTTP_UNAUTHORIZED"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="HTTP_NOT_FOUND"
 *     ),
 *     @SWG\Response(
 *         response=405,
 *         description="HTTP_METHOD_NOT_ALLOWED"
 *     ),
 *     @SWG\Response(
 *         response=500,
 *         description="HTTP_INTERNAL_SERVER_ERROR"
 *     )
 * )
 */
