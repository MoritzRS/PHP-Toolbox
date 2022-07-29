<?php

(function () {
    $filesDir = FILES_DIR;
    $content = <<<htaccess
    Options -Indexes
    <IfModule mod_rewrite.c>
        RewriteEngine on
        RewriteRule !^{$filesDir}($|/) index.php [L]
    </IfModule>
    htaccess;
    file_put_contents(".htaccess", $content);
})();
