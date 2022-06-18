<?php

(function () {
    $content = <<<htaccess
    <IfModule mod_rewrite.c>
        RewriteEngine on
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ /index.php [L,QSA]
    </IfModule>
    htaccess;
    file_put_contents(".htaccess", $content);
})();
