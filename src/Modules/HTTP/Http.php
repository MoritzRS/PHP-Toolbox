<?php

namespace Modules\HTTP;

class Http {
    // Status Codes
    public const Success = 200;
    public const BadRequest = 400;
    public const Unauthorized = 401;
    public const Forbidden = 403;
    public const NotFound = 404;
    public const ServerError = 500;

    /**
     * Set Default Headers
     */
    public static function setHeaders() {
        header("Access-Control-Allow-Methods: *");
        header("Allow: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Credentials: true");
        header("Content-Type: application/json; charset=utf-8");

        // CORS
        if (isset($_SERVER["HTTP_ORIGIN"])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        } else {
            header("Access-Control-Allow-Origin: *");
        }

        // Headers
        if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"])) {
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        } else {
            header("Access-Control-Allow-Headers: Authorization, Content-Type, Accept, Origin, X-Requested-With");
        }
    }
}
