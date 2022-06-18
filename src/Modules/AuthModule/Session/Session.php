<?php

namespace Modules\AuthModule\Session;

use Modules\AuthModule\User\User;
use Tools\Database\Database;
use Tools\IDGenerator\IDGenerator;

class Session {
    private string $id;
    private User $user;
    private string $timestamp;

    private function __construct(string $id, User $user, string $timestamp) {
        $this->id = $id;
        $this->user = $user;
        $this->timestamp = $timestamp;
    }

    /**
     * Creates a new session for the given user
     * @param User $user Session user
     * @return Session|false
     */
    public static function create(User $user) {
        $idGenerator = new IDGenerator(32);
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();

        $timestamp = date("Y-m-d H:i:s");
        $id = $idGenerator->next();
        while ($db->fetchSingle("SELECT * FROM SESSIONS WHERE id = :id", [":id" => $id])) {
            $id = $idGenerator->next();
        }

        $db->clearException();

        $db->execute("DELETE FROM SESSIONS WHERE user = :uid", [":uid" => $user->getID()]);
        $db->execute(
            "INSERT INTO SESSIONS(id, user, timestamp) VALUES(:id, :uid, :timestamp);",
            [
                ":id" => $id,
                ":uid" => $user->getID(),
                ":timestamp" => $timestamp
            ]
        );

        if ($db->hasException()) {
            var_dump($db->getException()->getMessage());
            $db->rollBack();
            return false;
        }

        $db->commit();
        return new self($id, $user, $timestamp);
    }

    /**
     * Finds and returns session by its id
     * @param string $id Session ID
     * @return Session|false
     */
    public static function getByID(string $id) {
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $data = $db->fetchSingle(
            "SELECT id, user, timestamp FROM SESSIONS WHERE id = :id;",
            [":id" => $id]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $user = User::getByID(intval($data["user"]));
        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        return new self(
            $data["id"],
            $user,
            $data["timestamp"]
        );
    }

    /**
     * Finds and returns session by its User
     * @param User $user Session User
     * @return Session|false
     */
    public static function getByUser(User $user) {
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $data = $db->fetchSingle(
            "SELECT id, user, timestamp FROM SESSIONS WHERE user = :uid;",
            [":uid" => $user->getID()]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $user = User::getByID(intval($data["user"]));
        if (!$user) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        return new self(
            $data["id"],
            $user,
            $data["timestamp"]
        );
    }

    /**
     * Invalidates all sessions by a user
     * @param User $user Session User
     * @return Session|false
     */
    public static function deleteFromUser(User $user) {
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $db->execute("DELETE FROM SESSIONS WHERE user = :uid;", [":uid" => $user->getID()]);

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        return true;
    }

    /**
     * Invalidates all sessions
     * @return Session|false
     */
    public static function deleteAll() {
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $db->execute("DELETE FROM SESSIONS;");

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        return true;
    }

    /**
     * Invalidates all sessions
     * @return Session|false
     */
    public static function deleteExpired() {
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $db->execute(
            "DELETE FROM SESSIONS WHERE timestamp < :expiration;",
            [":expiration" => date("Y-m-d H:i:s", time() - (60 * AUTH_MODULE_SESSION_DURATION))]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        return true;
    }

    /**
     * Get Session ID
     * @return string
     */
    public function getID() {
        return $this->id;
    }

    /**
     * Gets Session User
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Checks if Session expired
     * @return bool
     */
    public function isExpired() {
        return $this->timestamp < date("Y-m-d H:i:s", time() - (60 * AUTH_MODULE_SESSION_DURATION));
    }

    /**
     * Deletes the Session
     * @return bool
     */
    public function delete() {
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $db->execute(
            "DELETE FROM SESSIONS WHERE id = :id;",
            [":id" => $this->id]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        return true;
    }
}
