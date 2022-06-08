<?php

use Tools\IDGenerator\IDGenerator;
use Tools\IDGenerator\IDModes;
use Tools\Test\Test;
use Tools\Test\Assert;

$test = new Test(IDGenerator::class);

$test->test("Default", function () {
    $generator = new IDGenerator();
    $id = $generator->next();

    Assert::true(is_string($id));
    Assert::equals(strlen($id), 16);

    $characters = "0123456789abcdef";
    foreach (str_split($id) as $character) {
        Assert::true(strpos($characters, $character) !== false);
    }

    Assert::notEquals($id, $generator->next());
});

$test->test("Hexadecimal", function () {
    $generator = new IDGenerator(10, IDModes::Hexadecimal);
    $id = $generator->next();

    Assert::true(is_string($id));
    Assert::equals(strlen($id), 10);

    $characters = "0123456789abcdef";
    foreach (str_split($id) as $character) {
        Assert::true(strpos($characters, $character) !== false);
    }

    Assert::notEquals($id, $generator->next());
});

$test->test("Decimal", function () {
    $generator = new IDGenerator(10, IDModes::Decimal);
    $id = $generator->next();

    Assert::true(is_string($id));
    Assert::equals(strlen($id), 10);

    $characters = "0123456789";
    foreach (str_split($id) as $character) {
        Assert::true(strpos($characters, $character) !== false);
    }

    Assert::notEquals($id, $generator->next());
});

$test->test("Alphabetical", function () {
    $generator = new IDGenerator(10, IDModes::Alphabetical);
    $id = $generator->next();

    Assert::true(is_string($id));
    Assert::equals(strlen($id), 10);

    $characters = "abcdefghijklmnopqrstuvwxyz";
    foreach (str_split($id) as $character) {
        Assert::true(strpos($characters, $character) !== false);
    }

    Assert::notEquals($id, $generator->next());
});

$test->test("Alphanumerical", function () {
    $generator = new IDGenerator(10, IDModes::Alphabetical);
    $id = $generator->next();

    Assert::true(is_string($id));
    Assert::equals(strlen($id), 10);

    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    foreach (str_split($id) as $character) {
        Assert::true(strpos($characters, $character) !== false);
    }

    Assert::notEquals($id, $generator->next());
});

$test->run();
