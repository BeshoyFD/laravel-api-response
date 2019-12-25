<?php
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use LaravelApiResponse\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
return [
	'exception_details' => !env('APP_DEBUG'),
	'request_path' => "api/*",
	'exceptions' => [],
	'handle_exceptions' => [

		ModelNotFoundException::class => function (ModelNotFoundException $e, JsonResponse $json) {
			return $json->setStatusCode(Response::HTTP_NOT_FOUND)->set("message" , "No query results of model.");
		},

		ValidationException::class => function (ValidationException $e, JsonResponse $json) {
			return $json->mergeErrors($e->errors())->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)->set("message" , $e->getMessage());
		},

		AuthorizationException::class => function (AuthorizationException $e, JsonResponse $json) {
			return $json->setStatusCode(Response::HTTP_FORBIDDEN)->set("message" , $e->getMessage());
		},
		AccessDeniedHttpException::class => function (AccessDeniedHttpException $e, JsonResponse $json) {
			return $json->setStatusCode(Response::HTTP_FORBIDDEN)->set("message" , $e->getMessage());
		},

		AuthenticationException::class => function (AuthenticationException $e, JsonResponse $json) {
			return $json->setStatusCode(Response::HTTP_UNAUTHORIZED)->set("message" , $e->getMessage());
		},

		BadMethodCallException::class => function (BadMethodCallException $e, JsonResponse $json) {
			return $json->setStatusCode(Str::json_response_statusCode_exception($e))->set("message" , $e->getMessage());
		},
		MethodNotAllowedHttpException::class => function (MethodNotAllowedHttpException $e, JsonResponse $json) {
			return $json->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED)->set("message" , $e->getMessage());
		},

		HttpException::class => function (HttpException $e, JsonResponse $json) {
			return $json->setStatusCode(Str::json_response_statusCode_exception($e))->set("message" , $e->getMessage() ?: 'Endpoint Not Found. If error persists, contact developer.');
		},
		ErrorException::class => function (ErrorException $e, JsonResponse $json) {
			return $json->setStatusCode(Str::json_response_statusCode_exception($e))->set("message" , $e->getMessage());
		},
		Exception::class => function (Exception $e, JsonResponse $json) {
			return $json->setStatusCode(Str::json_response_statusCode_exception($e))->set("message" , $e->getMessage());
		},
		/**
		 * Add all the errors from the validation and continue
		 */

	]
];