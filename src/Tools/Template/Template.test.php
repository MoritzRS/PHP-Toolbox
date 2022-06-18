<?php

use Tools\Template\Template;
use Tools\Test\Test;
use Tools\Test\Assert;

$test = new Test(Template::class);

$test->test("Primitives", function () {

    $mixed_template = "mixed";
    $mixed_validator = new Template($mixed_template);
    Assert::true($mixed_validator->validate("Hello World"));
    Assert::true($mixed_validator->validate(123));
    Assert::true($mixed_validator->validate(true));
    Assert::true($mixed_validator->validate([]));
    Assert::true($mixed_validator->validate((object)[]));

    $string_template = "string";
    $string_validator = new Template($string_template);
    Assert::true($string_validator->validate("Hello World"));
    Assert::false($string_validator->validate(123));

    $integer_template = "integer";
    $integer_validator = new Template($integer_template);
    Assert::true($integer_validator->validate(123));
    Assert::false($integer_validator->validate("123"));

    $float_template = "float";
    $float_validator = new Template($float_template);
    Assert::true($float_validator->validate(123.0));
    Assert::false($float_validator->validate(123));

    $number_template = "number";
    $number_validator = new Template($number_template);
    Assert::true($number_validator->validate(123.0));
    Assert::true($number_validator->validate(123));
    Assert::false($number_validator->validate("123"));

    $numeric_template = "numeric";
    $numeric_validator = new Template($numeric_template);
    Assert::true($numeric_validator->validate(123.0));
    Assert::true($numeric_validator->validate(123));
    Assert::true($numeric_validator->validate("123"));
    Assert::false($numeric_validator->validate("Hello World"));

    $boolean_template = "boolean";
    $boolean_validator = new Template($boolean_template);
    Assert::true($boolean_validator->validate(true));
    Assert::true($boolean_validator->validate(false));
    Assert::false($boolean_validator->validate(1));
    Assert::false($boolean_validator->validate("Hello World"));
});

$test->test("Objects", function () {
    $object_template = (object)["text" => "string", "digits" => "integer"];
    $object_validator = new Template($object_template);
    Assert::true($object_validator->validate((object)["text" => "Hello World", "digits" => 123]));
    Assert::false($object_validator->validate(["text" => "Hello World", "digits" => 123]));
    Assert::false($object_validator->validate((object)["text" => 123, "digits" => "Hello World"]));
});

$test->test("Arrays", function () {
    $array_template = ["string"];
    $array_validator = new Template($array_template);
    Assert::true($array_validator->validate(["Hello World", "Hello World"]));
    Assert::true($array_validator->validate([]));
    Assert::false($array_validator->validate(["Hello World", 123]));
});

$test->test("Create fromJSON", function () {
    $template = Template::fromJSON(__DIR__ . "/template.test.json");
    Assert::true($template->validate((object)["msg" => "Hello World"]));
    Assert::false($template->validate("Hello World"));
});

$test->run();
