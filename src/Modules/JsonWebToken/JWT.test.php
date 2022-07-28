<?php

use Modules\JsonWebToken\JWT;
use Testing\Test;
use Testing\Assert;

$test = new Test(JWT::class);

$test->test("Generate and Validate", function () {
    $target = new JWT("SECRET");
    $validation = new JWT("SECRET", $target->getToken());
    Assert::true($validation->isValid());
});

$test->test("Invalid Signature", function () {
    $target = new JWT("SECRET");

    $validation = new JWT("TOPSECRET", $target->getToken());
    Assert::false($validation->isValid());
});

$test->test("Invalid Token", function () {
    $validation = new JWT("SECRET", "garbage");
    Assert::false($validation->isValid());
    Assert::equals($validation->get("msg"), null);
});

$test->test("Access Data", function () {
    $target = new JWT("SECRET");
    $target->set(["msg" => "Hello There"]);

    $receiver = new JWT("SECRET", $target->getToken());
    Assert::equals($receiver->get("msg"), "Hello There");
});

$test->run();
