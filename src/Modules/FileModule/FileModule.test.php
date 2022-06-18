<?php

require(__DIR__ . "/config.php");


// require all targeted files that end with .test.php
global $argv;
(function ($target) {
    $iterator = new RecursiveDirectoryIterator(__DIR__);
    foreach (new RecursiveIteratorIterator($iterator) as $file => $__) {
        if (!is_file($file)) continue;
        if (is_dir($file)) continue;

        // str_ends_with not supported
        $extension = ".test_m.php";
        if (strpos($file, $extension) !== strlen($file) - strlen($extension)) continue;

        if (!$target) {
            require_once($file);
            continue;
        }

        // str_contains not supported
        if (strpos($file, $target) !== false) require_once($file);
    }
})($argv[2] ?? false);
