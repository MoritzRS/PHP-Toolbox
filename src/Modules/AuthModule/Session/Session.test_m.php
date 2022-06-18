<?php

use Modules\AuthModule\Session\Session;
use Modules\AuthModule\User\AccessLevels;
use Modules\AuthModule\User\User;
use Tools\Database\Database;
use Tools\Test\Test;
use Tools\Test\Assert;

$test = new Test(Session::class);

$test->beforeAll(function () {
    require(__DIR__ . "/../setup.php");
});

$test->afterAll(function () {
    unlink(AUTH_MODULE_DB);
    rmdir(dirname(AUTH_MODULE_DB));
});

$test->test("Create Session", function () {
    $user = User::create("TestUser", "some@mail.com", "12345678", AccessLevels::None);
    $session = Session::create($user);

    Assert::notEquals($session, false);
    Assert::equals($session->getUser(), $user);
    Assert::false($session->isExpired());

    $db = new Database(AUTH_MODULE_DB);
    $data = $db->fetch("SELECT * FROM SESSIONS;");
    Assert::equals(count($data), 1);
});

$test->test("One Session per User", function () {
    $user = User::create("TestUser", "some@mail.com", "12345678", AccessLevels::None);
    Session::create($user);
    $session = Session::create($user);

    Assert::notEquals($session, false);
    Assert::equals($session->getUser(), $user);
    Assert::false($session->isExpired());

    $db = new Database(AUTH_MODULE_DB);
    $data = $db->fetch("SELECT * FROM SESSIONS;");
    Assert::equals(count($data), 1);
});

$test->test("Delete Sessions", function () {
    $db = new Database(AUTH_MODULE_DB);
    $user = User::create("TestUser", "some@mail.com", "12345678", AccessLevels::None);

    $session = Session::create($user);
    Assert::equals(1, count($db->fetch("SELECT * FROM SESSIONS;")));
    $session->delete();
    Assert::equals(0, count($db->fetch("SELECT * FROM SESSIONS;")));

    $session = Session::create($user);
    Assert::equals(1, count($db->fetch("SELECT * FROM SESSIONS;")));
    Session::deleteFromUser($user);
    Assert::equals(0, count($db->fetch("SELECT * FROM SESSIONS;")));

    $session = Session::create($user);
    Assert::equals(1, count($db->fetch("SELECT * FROM SESSIONS;")));
    Session::deleteAll();
    Assert::equals(0, count($db->fetch("SELECT * FROM SESSIONS;")));
});

$test->test("Get Session", function () {
    $user = User::create("TestUser", "some@mail.com", "12345678", AccessLevels::None);
    $session = Session::create($user);

    $byID = Session::getByID($session->getID());
    $byEmptyID = Session::getByID("");
    $byUser = Session::getByUser($user);

    Assert::false($byEmptyID);
    Assert::equals($session, $byID);
    Assert::equals($session, $byUser);
    Assert::equals($byID, $byUser);
});

$test->run();
