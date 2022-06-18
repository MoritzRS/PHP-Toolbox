<?php

namespace Tools\HTTP;

class HttpHeaders {

    public static function origin(string $origin = "*") {
        header("Access-Control-Allow-Origin: {$origin}");
    }

    public static function creditdentials(bool $allow = true) {
        $value = $allow ? "true" : "false";
        header("Access-Control-Allow-Credentials: {$value}");
    }

    public static function methods(string ...$args) {
        $methods = implode(", ", $args);
        header("Access-Control-Allow-Methods: {$methods}");
    }

    public static function headers(string ...$args) {
        $headers = implode(", ", $args);
        header("Access-Control-Allow-Headers: {$headers}");
    }

    public static function content(string $type = "application/json", string $charset = "utf-8") {
        header("Content-Type: {$type}; charset={$charset}");
    }
}
