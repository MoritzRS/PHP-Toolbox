<?php

use Modules\Database\Database;
use Testing\Test;
use Testing\Assert;

$test = new Test(Database::class);

$test->beforeAll(function () {
    file_put_contents(__DIR__ . "/testdb.sqlite", "");
});

$test->afterAll(function () {
    unlink(__DIR__ . "/testdb.sqlite");
});

$test->test("Connect to Database", function () {
    $db = new Database(__DIR__ . "/testdb.sqlite");
});

$test->test("Execute on Database", function () {
    $db = new Database(__DIR__ . "/testdb.sqlite");
    $db->execute("CREATE TABLE TEST(text TEXT PRIMARY KEY);");
    $db->execute("INSERT INTO TEST(text) VALUES(:text);", [":text" => "Hello World"]);
    Assert::false($db->hasException());
});

$test->test("Fetch from Database", function () {
    $db = new Database(__DIR__ . "/testdb.sqlite");
    $db->execute("CREATE TABLE TEST(text TEXT PRIMARY KEY);");
    $db->execute("INSERT INTO TEST(text) VALUES(:text);", [":text" => "Hello World"]);
    Assert::false($db->hasException());

    $result = $db->fetch("SELECT * FROM TEST;");
    $expected = [["text" => "Hello World"]];
    Assert::equals($result, $expected);
    Assert::false($db->hasException());

    $fail = $db->fetch("SELECT * FROM ANOTHERTABLE;");
    Assert::false($fail);
    Assert::true($db->hasException());
});

$test->test("Fetch Single from Database", function () {
    $db = new Database(__DIR__ . "/testdb.sqlite");
    $db->execute("CREATE TABLE TEST(text TEXT PRIMARY KEY);");
    $db->execute("INSERT INTO TEST(text) VALUES(:text);", [":text" => "Hello World"]);
    Assert::false($db->hasException());

    $result = $db->fetchSingle("SELECT * FROM TEST;");
    $expected = ["text" => "Hello World"];
    Assert::equals($result, $expected);
    Assert::false($db->hasException());

    $fail = $db->fetch("SELECT * FROM ANOTHERTABLE;");
    Assert::false($fail);
    Assert::true($db->hasException());
});

$test->test("Transactions", function () {
    $db = new Database(__DIR__ . "/testdb.sqlite");
    $db->beginTransaction();
    $db->execute("CREATE TABLE TEST(text TEXT PRIMARY KEY);");
    $db->execute("INSERT INTO TEST(text) VALUES(:text);", [":text" => "Hello World"]);
    Assert::false($db->hasException());
    $db->rollBack();

    $result = $db->fetch("SELECT * FROM TEST;");
    Assert::false($result);
    Assert::true($db->hasException());
});

$test->test("Null vs Empty String", function () {
    $db = new Database(__DIR__ . "/testdb.sqlite");
    $db->execute("CREATE TABLE TEST(col1 TEXT, col2 TEXT);");
    $db->execute("INSERT INTO TEST VALUES('', NULL);");
    $data = $db->fetchSingle("SELECT * FROM TEST;");
    Assert::strictEquals($data, ["col1" => "", "col2" => null]);
});

$test->run();
