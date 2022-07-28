<?php

use Testing\Test;
use Testing\Assert;
use Modules\Database\Database;
use Modules\Database\TableInterface;

$test = new Test(TableInterface::class);

$test->beforeAll(function () {
    file_put_contents(__DIR__ . "/testdb.sqlite", "");
    $db = new Database(__DIR__ . "/testdb.sqlite");
    $db->execute("CREATE TABLE Posts(
        title TEXT,
        content TEXT
    );");
});

$test->afterAll(function () {
    unlink(__DIR__ . "/testdb.sqlite");
});


class Post extends TableInterface {
    protected static $databasePath = __DIR__ . "/testdb.sqlite";
    protected static $tableName = "Posts";
    protected static $defaultProperties = [
        "title" => "",
        "content" => ""
    ];
}

$test->test("Create, Set, Get", function () {
    $post = new Post([
        "title" => "Hello World",
        "content" => "This is a test"
    ]);

    Assert::equals($post->get("title"), "Hello World");
    Assert::equals($post->get("content"), "This is a test");
    Assert::equals($post->get("something"), null);
    Assert::equals($post->get(["title"]), ["title" => "Hello World"]);
    Assert::equals($post->get(["title", "content"]), ["title" => "Hello World", "content" => "This is a test"]);
    Assert::equals($post->get(["something"]), ["something" => null]);

    $post->set([
        "title" => "Welcome",
        "content" => "And this also"
    ]);

    Assert::equals($post->get("title"), "Welcome");
    Assert::equals($post->get("content"), "And this also");
});

$test->test("Save and Find", function () {
    Assert::false(Post::find(["title" => "Hello World"]));

    $post = new Post([
        "title" => "Hello World",
        "content" => "This is a test"
    ]);
    $post->save();

    Assert::equals(Post::find(["title" => "Hello World"]), $post);

    $post->set([
        "title" => "Hello You too"
    ]);
    $post->save();

    Assert::false(Post::find(["title" => "Hello World"]));
    Assert::equals(Post::find(["title" => "Hello You too"]), $post);
    Assert::equals(Post::find(["title = 'Hello You too'"]), $post);
});


$test->test("Find All", function () {
    $post1 = new Post(["title" => "Post1", "content" => "Content1"]);
    $post1->save();

    $post2 = new Post(["title" => "Post2", "content" => "Content2"]);
    $post2->save();

    $posts = Post::findAll();
    Assert::equals($posts[0], $post1);
    Assert::equals($posts[1], $post2);

    Assert::equals(Post::findAll([], [], 1), [$post1]);
    Assert::equals(Post::findAll([], ["rowid DESC"], 1), [$post2]);
});

$test->test("Filter", function () {
    $post1 = new Post(["title" => "Post1", "content" => "Content1"]);
    $post1->save();

    $post2 = new Post(["title" => "Post2", "content" => "Content2"]);
    $post2->save();

    $posts = Post::filter(["title", "xyz"], [], [], 1);
    Assert::equals($posts[0]->get("title"), "Post1");
    Assert::equals($posts[0]->get("content"), null);
});

$test->test("Delete", function () {
    $post = new Post(["title" => "Hello World", "content" => "Some Content"]);
    $post->save();

    Assert::equals(Post::find(["title" => "Hello World"]), $post);

    $post->delete();

    Assert::false(Post::find(["title" => "Hello World"]));
});

$test->test("Has", function () {
    Assert::false(Post::has());

    $post = new Post([
        "title" => "Hello World",
        "content" => "This is a test"
    ]);
    $post->save();

    Assert::true(Post::has());
    Assert::true(Post::has(["title" => "Hello World"]));

    $post->delete();

    Assert::false(Post::has());
    Assert::false(Post::has(["title" => "Hello World"]));
});

$test->run();
