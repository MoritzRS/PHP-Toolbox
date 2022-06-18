<?php

use Modules\AuthModule\User\AccessLevels;
use Modules\AuthModule\User\User;
use Tools\Database\Database;
use Tools\Test\Test;
use Tools\Test\Assert;

$test = new Test(User::class);

$test->beforeAll(function () {
    require(__DIR__ . "/../setup.php");
});

$test->afterAll(function () {
    unlink(AUTH_MODULE_DB);
    rmdir(dirname(AUTH_MODULE_DB));
});

$test->test("Create User", function () {
    $user = User::create("TestUser", "some@mail.com", "12345678", AccessLevels::None);
    Assert::notEquals($user, false);

    $db = new Database(AUTH_MODULE_DB);
    $data = $db->fetch("SELECT * FROM USERS;");
    Assert::equals(count($data), 1);
    Assert::equals($user->getName(), "TestUser");
    Assert::equals($user->getEmail(), "some@mail.com");
    Assert::equals($user->getAccessLevel(), AccessLevels::None);
    Assert::true($user->verifyPassword("12345678"));
});

$test->test("Get User", function () {
    $user = User::create("TestUser", "some@mail.com", "12345678", AccessLevels::None);

    $byID = User::getByID($user->getID());
    $byName = User::getByName($user->getName());

    Assert::equals($user, $byID);
    Assert::equals($user, $byName);
    Assert::equals($byID, $byName);
});

$test->test("Set Data", function () {
    $user = User::create("TestUser", "some@mail.com", "12345678", AccessLevels::None);

    Assert::true($user->setName("AnotherUser"));
    Assert::true($user->setEmail("other@mail.com"));
    Assert::true($user->setAccessLevel(AccessLevels::Admin));
    Assert::true($user->setPassword("1234"));

    $byID = User::getByID($user->getID());

    Assert::equals($user, $byID);
    Assert::equals($byID->getName(), "AnotherUser");
    Assert::equals($byID->getEmail(), "other@mail.com");
    Assert::equals($byID->getAccessLevel(), AccessLevels::Admin);
    Assert::true($byID->verifyPassword("1234"));
});

$test->test("Delete User", function () {
    $user = User::create("TestUser", "some@mail.com", "12345678", AccessLevels::None);

    $db = new Database(AUTH_MODULE_DB);
    $created = $db->fetch("SELECT * FROM USERS;");
    Assert::equals(count($created), 1);

    $user->delete();
    $deleted = $db->fetch("SELECT * FROM USERS;");
    Assert::equals(count($deleted), 0);
});

$test->run();
