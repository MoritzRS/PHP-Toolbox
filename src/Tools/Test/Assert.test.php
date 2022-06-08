<?php

use Tools\Test\Assert;
use Tools\Test\Test;

$test = new Test(Assert::class);

$test->test("Assert::equals", function () {
    $a = ["1" => "1", "2" => "2"];
    $b = ["2" => "2", "1" => "1"];
    $c = ["3" => "3", "4" => "4"];

    Assert::equals($a, $b);
    Assert::equals($b, $a);
    Assert::fail(function () use ($a, $c) {
        Assert::equals($a, $c);
    });
});

$test->test("Assert::strictEquals", function () {
    $a = ["1" => "1", "2" => "2"];
    $b = ["2" => "2", "1" => "1"];
    $c = ["3" => "3", "4" => "4"];

    Assert::strictEquals($a, $a);
    Assert::fail(function () use ($a, $b) {
        Assert::strictEquals($a, $b);
    });
    Assert::fail(function () use ($a, $c) {
        Assert::strictEquals($a, $c);
    });
});

$test->test("Assert::notEquals", function () {
    $a = ["1" => "1", "2" => "2"];
    $b = ["3" => "3", "4" => "4"];
    $c = ["2" => "2", "1" => "1"];

    Assert::notEquals($a, $b);
    Assert::notEquals($b, $a);
    Assert::fail(function () use ($a, $c) {
        Assert::notEquals($a, $c);
    });
});

$test->test("Assert::strictNotEquals", function () {
    $a = ["1" => "1", "2" => "2"];
    $b = ["1" => "1", "2" => "2"];
    $c = ["2" => "2", "1" => "1"];

    Assert::strictNotEquals($a, $c);
    Assert::fail(function () use ($a, $b) {
        Assert::strictNotEquals($a, $b);
    });
});


$test->test("Assert::true", function () {
    Assert::true(true);
    Assert::fail(function () {
        Assert::true(false);
    });
});

$test->test("Assert::false", function () {
    Assert::false(false);
    Assert::fail(function () {
        Assert::false(true);
    });
});

$test->test("Assert::null", function () {
    Assert::null(null);
    Assert::fail(function () {
        Assert::null(false);
    });
});

$test->test("Assert::notNull", function () {
    Assert::notNull(false);
    Assert::fail(function () {
        Assert::notNull(null);
    });
});

$test->test("Assert::fail", function () {
    Assert::fail(function () {
        throw new Exception("FAIL");
    });

    Assert::fail(function () {
        Assert::fail(function () {
            return;
        });
    });
});

$test->run();
