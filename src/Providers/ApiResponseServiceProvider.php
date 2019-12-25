<?php

namespace LaravelApiResponse\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use LaravelApiResponse\JsonResponse;
use Illuminate\Routing\ResponseFactory;

class ApiResponseServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot ()
    {


	   $this->publishes([__DIR__ . '/../config/config.php' => config_path(LARAVEL_JSON_RESPONSE_CONFIG . '.php')]);

        $this->app->singleton(LARAVEL_JSON_RESPONSE_KEY, function () {
            return new JsonResponse();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register ()
    {
		
		 foreach (glob(__DIR__.'/../Helpers/*.php') as $filename){
			 
			require_once($filename);
			
		}
		
	    //check if exception has statusCode , if not ,  return error 500
	    Str::macro("json_response_statusCode_exception", function($exception, $setStatusCode = Response::HTTP_INTERNAL_SERVER_ERROR){
		    return  method_exists($exception,'getStatusCode') ? $exception->getStatusCode() :  $setStatusCode;
	    });

	    //api response for success / errors / messages
	    $api_response = function($content = null,$errors = null,$statusCode = Response::HTTP_UNPROCESSABLE_ENTITY){

		    if(!$errors && $content)
			    return Response($content,Response::HTTP_OK);

		    if($errors && !is_array($errors) && ctype_digit((string)$errors)) // or maybe its status code
			    return Response($content,$errors);

		    $json_response = json_response();
		    if(!empty($errors)) {
			    if($content) {
				    $json_response->set($content);
			    }
			    return $json_response->setStatusCode($statusCode)->setError($errors);
		    }
		    return $json_response;
	    };

	    Response::macro("api", $api_response);
	    ResponseFactory::macro("api", $api_response);

	    $this->mergeConfigFrom(
            __DIR__ . '/../config/config.php', LARAVEL_JSON_RESPONSE_CONFIG
        );
    }
}