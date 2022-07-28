<?php

use Modules\Routing\Router;
use Testing\Test;
use Testing\Assert;

$test = new Test(Router::class);

$test->test("Create and Evaluate Endpoint", function () {
    $_SERVER["REQUEST_METHOD"] = "GET";
    $_SERVER["REQUEST_URI"] = "http://localhost/backend/posts/mrs/123";

    Router::reset();
    Router::setBaseURL("/backend");

    $result = Router::get("/posts/{username}/{id}", function ($username, $id) {
        return $username . ":" . $id;
    });

    Assert::equals($result, "mrs:123");
});

$test->test("Create and Evaluate Any Endpoint", function () {
    $_SERVER["REQUEST_METHOD"] = "GET";
    $_SERVER["REQUEST_URI"] = "http://localhost/backend/posts/mrs/123";

    Router::reset();
    Router::setBaseURL("/backend");

    $result = Router::any("/posts/{username}/{id}", function ($username, $id) {
        return $username . ":" . $id;
    });

    Assert::equals($result, "mrs:123");
});

$test->test("Create and Evaluate Matching Endpoint", function () {
    $_SERVER["REQUEST_METHOD"] = "GET";
    $_SERVER["REQUEST_URI"] = "http://localhost/backend/posts/mrs/123";

    Router::reset();
    Router::setBaseURL("/backend");

    $result = Router::match(["get", "post"], "/posts/{username}/{id}", function ($username, $id) {
        return $username . ":" . $id;
    });

    Assert::equals($result, "mrs:123");
});

$test->test("Create and Evaluate Endpoint without parameters and different method", function () {
    $_SERVER["REQUEST_METHOD"] = "POST";
    $_SERVER["REQUEST_URI"] = "http://localhost/backend/posts";

    Router::reset();
    Router::setBaseURL("/backend");

    $result = Router::post("/posts", function () {
        return "123";
    });

    Assert::equals($result, "123");
});

$test->test("Create and Evaluate Wrong Endpoint", function () {
    $_SERVER["REQUEST_METHOD"] = "GET";
    $_SERVER["REQUEST_URI"] = "http://localhost/backend/pages";

    Router::reset();
    Router::setBaseURL("/backend");

    $result = Router::get("/posts", function () {
        return "123";
    });

    Assert::false($result);
});

$test->test("Custom Regex", function () {
    $_SERVER["REQUEST_METHOD"] = "GET";
    $_SERVER["REQUEST_URI"] = "http://localhost/backend/pages/test";

    Router::reset();
    Router::setBaseURL("/backend");

    // Not Matching because only numbers allowed
    $false = Router::get("/pages/{id:\d+}", function ($id) {
        return $id;
    });
    Assert::false($false);

    // Matching with only letters in lowercase
    $result = Router::get("/pages/{id:[a-z]+}", function ($id) {
        return $id;
    });
    Assert::equals($result, "test");
});

$test->test("Create and Evaluate Fallback", function () {
    $_SERVER["REQUEST_METHOD"] = "GET";
    $_SERVER["REQUEST_URI"] = "http://localhost/backend/pages";

    Router::reset();
    Router::setBaseURL("/backend");

    $false = Router::get("/posts", function () {
        return "123";
    });
    Assert::false($false);

    $result2 = Router::fallback(function () {
        return "123";
    });
    Assert::equals($result2, "123");
});

$test->run();
