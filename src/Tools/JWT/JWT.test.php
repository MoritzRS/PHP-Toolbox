<?php

use Tools\JWT\JWTGenerator;
use Tools\JWT\JWTReader;
use Tools\Test\Test;
use Tools\Test\Assert;

$test = new Test(JWTGenerator::class . " & " . JWTReader::class);

$test->test("Generate and Validate", function () {
    $generator = new JWTGenerator("SECRET");
    $payload = (object)["msg" => "Hello World"];
    $token = $generator->generate($payload);

    $reader = new JWTReader($token, "SECRET");
    Assert::true($reader->validate());
    Assert::equals($reader->payload(), $payload);
});

$test->test("Invalid Signature", function () {
    $generator = new JWTGenerator("SECRET");
    $payload = (object)["msg" => "Hello World"];
    $token = $generator->generate($payload);

    $reader = new JWTReader($token, "PUBLIC");
    Assert::false($reader->validate());
    Assert::false($reader->payload(), $payload);
});

$test->test("Invalid Payload", function () {
    $reader = new JWTReader("Hello World", "PUBLIC");
    Assert::false($reader->validate());
    Assert::false($reader->payload());
});

$test->run();
