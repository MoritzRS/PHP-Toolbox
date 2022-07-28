<?php

use Modules\Tools\RandomStringGenerator;
use Testing\Test;
use Testing\Assert;

$test = new Test(RandomStringGenerator::class);

$test->test("Generate Random String", function () {
    $characters = "abcdefg";
    $generator = new RandomStringGenerator($characters);

    $result = $generator->next(10);
    var_dump($result);
    Assert::equals(strlen($result), 10);
    foreach (str_split($result) as $char) {
        Assert::strictNotEquals(strpos($characters, $char), false);
    }
});

$test->run();
