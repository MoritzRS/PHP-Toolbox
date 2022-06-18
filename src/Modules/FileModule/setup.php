<?php

use Tools\Database\Database;

(function () {
    mkdir(FILE_MODULE_FILE_PATH, 0777, true);
    mkdir(dirname(FILE_MODULE_DB), 0777, true);
    file_put_contents(FILE_MODULE_DB, "");
    $db = new Database(FILE_MODULE_DB);

    $create_files = <<<SQL
    CREATE TABLE FILES(
        id TEXT PRIMARY KEY,
        name TEXT NOT NULL,
        path TEXT NOT NULL,
        extension TEXT NOT NULL
    );
    SQL;
    $db->execute($create_files);
})();
