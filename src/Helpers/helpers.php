<?php

define('LARAVEL_JSON_RESPONSE_CONFIG', 'api-response');
define('LARAVEL_JSON_RESPONSE_KEY', 'laravel-api-response');

if (!function_exists('json_response')) {

    function json_response ()
    {
        return app(LARAVEL_JSON_RESPONSE_KEY);
    }
}