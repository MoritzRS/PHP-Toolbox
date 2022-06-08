<?php
// test runner

require("./config.php");
require("./autoloader.php");


// require all targeted files that end with .test.php
(function ($target) {
    $iterator = new RecursiveDirectoryIterator("./");
    foreach (new RecursiveIteratorIterator($iterator) as $file => $__) {
        if (!is_file($file)) continue;
        if (is_dir($file)) continue;

        // str_ends_with not supported
        $extension = ".test.php";
        if (strpos($file, $extension) !== strlen($file) - strlen($extension)) continue;

        if (!$target) {
            require_once($file);
            continue;
        }

        // str_contains not supported
        if (strpos($file, $target) !== false) require_once($file);
    }
})($argv[1] ?? false);
