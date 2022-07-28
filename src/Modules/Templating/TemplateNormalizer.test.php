<?php

use Modules\Templating\TemplateNormalizer;
use Testing\Test;
use Testing\Assert;

$test = new Test(TemplateNormalizer::class);

$test->test("Primitives", function () {
    $template = "string";
    $normalizer = new TemplateNormalizer($template);

    Assert::equals($normalizer->normalize("test"), "test");
    Assert::equals($normalizer->normalize(123), "string");
    Assert::equals($normalizer->normalize([]), "string");
    Assert::equals($normalizer->normalize((object)[]), "string");
    Assert::equals($normalizer->normalize(null), "string");
    Assert::equals($normalizer->normalize([]["test"]), "string");
});

$test->test("Arrays", function () {
    $template = ["string"];
    $normalizer = new TemplateNormalizer($template);

    Assert::equals($normalizer->normalize(["test"]), ["test"]);
    Assert::equals($normalizer->normalize("test"), ["string"]);
    Assert::equals($normalizer->normalize(["test", 123]), ["test", "string"]);
});

$test->test("Objects", function () {
    $template = (object)["key" => "string"];
    $normalizer = new TemplateNormalizer($template);

    Assert::equals($normalizer->normalize("string"), (object)["key" => "string"]);
    Assert::equals($normalizer->normalize((object)["key" => "test"]), (object)["key" => "test"]);
});

$test->test("Strict Mode", function () {
    $template = (object)["key" => "string"];
    $loose = new TemplateNormalizer($template);
    $strict = new TemplateNormalizer($template, true);

    Assert::equals($loose->normalize((object)["key" => "test", "other" => 123]), (object)["key" => "test", "other" => 123]);
    Assert::equals($strict->normalize((object)["key" => "test", "other" => 123]), (object)["key" => "test"]);
});

$test->run();
