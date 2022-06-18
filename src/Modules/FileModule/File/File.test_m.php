<?php

use Modules\FileModule\File\File;
use Tools\Database\Database;
use Tools\IDGenerator\IDGenerator;
use Tools\Test\Test;
use Tools\Test\Assert;

$test = new Test(File::class);

$test->beforeAll(function () {
    require(__DIR__ . "/../setup.php");
});

$test->afterAll(function () {
    unlink(FILE_MODULE_DB);
    $it = new RecursiveDirectoryIterator(FILE_MODULE_FILE_PATH, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
    rmdir(FILE_MODULE_FILE_PATH);
});

$test->test("Create, Get and Delete File", function () {
    $db = new Database(FILE_MODULE_DB);

    $id = (new IDGenerator())->next();
    $name = "TestFile.txt";
    $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $directory = FILE_MODULE_FILE_PATH . "/" . $extension;
    $path = $directory . "/" . $id . "." . $extension;

    $db->execute(
        "INSERT INTO FILES(id, name, path, extension) VALUES(:id, :name, :path, :extension);",
        [
            ":id" => $id,
            ":path" => $path,
            ":name" => $name,
            ":extension" => $extension
        ]
    );
    Assert::false($db->hasException());

    Assert::false(is_file($path));
    if (!is_dir($directory)) mkdir($directory, 0777, true);
    file_put_contents($path, "Hello World");
    Assert::true(is_file($path));

    $content = file_get_contents(File::getByID($id)->getPath());
    Assert::equals($content, "Hello World");

    $getByExt = File::getByExtensions(["txt"]);
    $getAll = File::getAll();
    Assert::equals($getByExt, $getAll);

    File::getByID($id)->delete();
    Assert::false(is_file($path));
    Assert::false(File::getByID($id));
});

$test->run();
