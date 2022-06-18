<?php

use Tools\Database\Database;

(function () {
    mkdir(dirname(AUTH_MODULE_DB), 0777, true);
    file_put_contents(AUTH_MODULE_DB, "");
    $db = new Database(AUTH_MODULE_DB);

    $create_users = <<<SQL
    CREATE TABLE USERS(
        id INTEGER PRIMARY KEY,
        name TEXT UNIQUE NOT NULL,
        email TEXT NOT NULL,
        hash TEXT NOT NULL,
        level INTEGER NOT NULL
    );
    SQL;
    $db->execute($create_users);

    $create_sessions = <<<SQL
    CREATE TABLE SESSIONS(
        id TEXT PRIMARY KEY,
        user INTEGER UNIQUE NOT NULL,
        timestamp TEXT NOT NULL
    );
    SQL;
    $db->execute($create_sessions);
})();
