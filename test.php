<?php
// test runner

require("./config.php");
require("./autoloader.php");

// require all files that end with .test.php
$iterator = new RecursiveDirectoryIterator("./");
foreach (new RecursiveIteratorIterator($iterator) as $file => $__) {
    if (!is_file($file)) continue;
    if (is_dir($file)) continue;
    if (!str_ends_with($file, ".test.php")) continue;
    require_once($file);
}
