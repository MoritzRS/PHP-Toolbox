<?php

use Tools\Procedure\Procedure;
use Tools\Procedure\Runtime;
use Tools\Template\Template;
use Tools\Test\Test;
use Tools\Test\Assert;

$test = new Test(Runtime::class);

$test->test("Create and evaluate", function () {
    $procedure = new class extends Procedure {

        public function __construct() {
            $this->template = new Template(["mixed"]);
        }

        protected function process($payload) {
            return $payload;
        }
    };

    $runtime = new Runtime([
        "testProcedure" => $procedure
    ]);

    $payload = ["msg" => "Hello World"];
    $result = $runtime->evaluate("testProcedure", $payload);
    Assert::equals($result, $payload);

    Assert::false($runtime->evaluate("something", $payload));
});

$test->test("Create, Register and evaluate", function () {
    $procedure = new class extends Procedure {

        public function __construct() {
            $this->template = new Template(["mixed"]);
        }

        protected function process($payload) {
            return $payload;
        }
    };

    $runtime = new Runtime();
    $runtime->register([
        "testProcedure" => $procedure
    ]);

    $payload = ["msg" => "Hello World"];
    $result = $runtime->evaluate("testProcedure", $payload);
    Assert::equals($result, $payload);

    Assert::false($runtime->evaluate("something", $payload));
});

$test->run();
