<?php

// registers autoloader
spl_autoload_register(function (string $class) {
    $base = "." . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR;
    $file = str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";
    if (file_exists($base . $file)) require($base . $file);
});
