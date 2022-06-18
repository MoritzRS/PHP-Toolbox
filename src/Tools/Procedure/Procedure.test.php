<?php

use Tools\Procedure\Procedure;
use Tools\Template\Template;
use Tools\Test\Test;
use Tools\Test\Assert;

$test = new Test(Procedure::class);

$test->test("Create and Execute", function () {
    $procedure = new class extends Procedure {

        protected function template() {
            return new Template(["mixed"]);
        }

        protected function process($payload) {
            return $payload;
        }
    };
    $payload = ["msg" => "Hello World"];
    $result = $procedure->execute($payload);
    Assert::equals($payload, $result);

    Assert::false($procedure->execute("Hello World"));
});

$test->run();
