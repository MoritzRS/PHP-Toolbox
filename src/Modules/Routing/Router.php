<?php

namespace Modules\Routing;

class Router {
    private static string $urlBase = "";
    private static bool $matched = false;

    /**
     * Resets the routers internal state.
     * Primarily used for testing
     */
    public static function reset() {
        static::$urlBase = "";
        static::$matched = false;
    }

    /**
     * Sets a base url to filter url matches.
     * Typically the dirname of the application
     * @param string $base BaseURL to filter. Typically does not end with a trailing slash
     */
    public static function setBaseURL(string $base) {
        static::$urlBase = $base;
    }

    /**
     * Evaluates GET callback
     * @param string $pattern URL Pattern to match
     * @param callable $callback
     * @return mixed|false
     */
    public static function get(string $pattern, callable $callback) {
        return static::evaluate(["get"], $pattern, $callback);
    }

    /**
     * Evaluates POST callback
     * @param string $pattern URL Pattern to match
     * @param callable $callback
     * @return mixed|false
     */
    public static function post(string $pattern, callable $callback) {
        return static::evaluate(["post"], $pattern, $callback);
    }

    /**
     * Evaluates PUT callback
     * @param string $pattern URL Pattern to match
     * @param callable $callback
     * @return mixed|false
     */
    public static function put(string $pattern, callable $callback) {
        return static::evaluate(["put"], $pattern, $callback);
    }

    /**
     * Evaluates DELETE callback
     * @param string $pattern URL Pattern to match
     * @param callable $callback
     * @return mixed|false
     */
    public static function delete(string $pattern, callable $callback) {
        return static::evaluate(["delete"], $pattern, $callback);
    }

    /**
     * Evaluates Any callback
     * @param string $pattern URL Pattern to match
     * @param callable $callback
     * @return mixed|false
     */
    public static function any(string $pattern, callable $callback) {
        return static::evaluate(["get", "post", "put", "delete"], $pattern, $callback);
    }

    public static function match(array $methods, string $pattern, callable $callback) {
        return static::evaluate(array_map(function ($e) {
            return strtolower($e);
        }, $methods), $pattern, $callback);
    }


    /**
     * Fallback Evaluation if API has no previous matches
     */
    public static function fallback(callable $callback) {
        if (static::$matched) return false;
        return call_user_func($callback);
    }

    /**
     * Evaluates the request and executes the callable
     * @param string $method Request Method to match
     * @param string $pattern URL Pattern to match
     * @param callable $callback Callback to execute when matching
     * @return mixed|false
     */
    private static function evaluate(array $methods, string $pattern, callable $callback) {
        if (!in_array(static::getMethod(), $methods)) return false;
        $url = static::getURL();
        if ($url === false) return false;
        $matching = static::matchURL($pattern, $url);
        if ($matching === false) return false;
        static::$matched = true;
        return call_user_func_array($callback, $matching);
    }

    /**
     * Returns the current requests method in lowercase
     * @return string
     */
    private static function getMethod() {
        return strtolower($_SERVER["REQUEST_METHOD"]);
    }

    /**
     * Returns the requests url in lowercase subtracted by the specified baseurl
     * @return string
     */
    private static function getURL() {
        $url = parse_url($_SERVER["REQUEST_URI"])["path"];
        if (strpos($url, static::$urlBase) === 0) $url = substr($url, strlen(static::$urlBase));
        else $url = false;
        return strtolower($url);
    }

    /**
     * Matches an url against a given pattern
     * @param string $pattern The url pattern with parameters
     * @param string $url The url to match
     * @return array|false Associative Array consisting of the matching results or false
     */
    private static function matchURL(string $pattern, string $url) {
        $pattern = trim($pattern, "/");
        $url = trim($url, "/");
        $keys = [];
        $values = [];

        // get keys from template
        if (preg_match_all('/\{(\w+)(:[^}]+)?}/', $pattern, $rawKeys)) $keys = $rawKeys[1];

        // build regex for finding keys in url
        $regex = "@^" . preg_replace_callback('/\{\w+(:([^}]+))?}/', function ($m) {
            return isset($m[2]) ? "({$m[2]})" : "(\w+)";
        }, $pattern) . "?$@";

        // extract values from url or return false
        if (!preg_match_all($regex, $url, $rawValues)) return false;
        $values = array_map(function ($e) {
            return $e[0];
        }, array_slice($rawValues, 1));
        return array_combine($keys, $values);
    }
}
