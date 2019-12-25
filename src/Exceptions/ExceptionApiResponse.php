<?php
/**
 * Created by PhpStorm.
 * User: Beshoy
 * Date: 25/12/2019 , 025
 * Time: 6:58 AM
 */

namespace LaravelApiResponse\Exceptions;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class ExceptionApiResponse
{

	private $apiRespoonse = null;
	private $foundReport = null;


	public function __construct($request, Exception $exception)
	{
		if($request->is(config(LARAVEL_JSON_RESPONSE_CONFIG . '.request_path')) && $exception && config(LARAVEL_JSON_RESPONSE_CONFIG . '.exception_details')) {
			$exceptions = config(LARAVEL_JSON_RESPONSE_CONFIG . '.handle_exceptions');
			foreach ($exceptions as $e => $case) {
				if (is_a($exception, $e)) {
					if (is_callable($case)) {
						$this->apiRespoonse = $case($exception, json_response());
						$this->foundReport = true;
						return;
					}
				}
			}
		}
	}


	public function report()
	{
		return $this->foundReport;

	}

	public function render()
	{
		return response()->json($this->apiRespoonse->toArray(),$this->apiRespoonse->getStatusCode());

	}
}