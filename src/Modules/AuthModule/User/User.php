<?php

namespace Modules\AuthModule\User;

use Tools\Database\Database;

class User {
    private int $id;
    private string $name;
    private string $email;
    private string $hash;
    private int $level;

    private function __construct(int $id, string $name, string $email, string $hash, int $level) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->hash = $hash;
        $this->level = $level;
    }

    /**
     * Creates a new user
     * @param string $name User name
     * @param string $email User email
     * @param string $password User password
     * @param int $accessLevel User access level
     * @return User|false
     */
    public static function create(string $name, string $email, string $password, int $accessLevel) {
        $db = new Database(AUTH_MODULE_DB);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $db->beginTransaction();
        $db->execute(
            "INSERT INTO USERS(name, email, hash, level) VALUES(:name, :email, :hash, :level);",
            [
                ":name" => $name,
                ":email" => $email,
                ":hash" => $hash,
                ":level" => $accessLevel
            ]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $uid = $db->fetchSingle("SELECT last_insert_rowid() as uid;");

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        return new self($uid["uid"], $name, $email, $hash, $accessLevel);
    }

    /**
     * Finds and returns user by its id
     * @param int $id User ID
     * @return User|false
     */
    public static function getByID(int $id) {
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $data = $db->fetchSingle(
            "SELECT id, name, email, hash, level FROM USERS WHERE id = :id;",
            [":id" => $id]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        return new self(
            intval($data["id"]),
            $data["name"],
            $data["email"],
            $data["hash"],
            intval($data["level"])
        );
    }

    /**
     * Finds and returns user by its name
     * @param string $name User name
     * @return User|false
     */
    public static function getByName(string $name) {
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $data = $db->fetchSingle(
            "SELECT id, name, email, hash, level FROM USERS WHERE name = :name;",
            [":name" => $name]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        return new self(
            intval($data["id"]),
            $data["name"],
            $data["email"],
            $data["hash"],
            intval($data["level"])
        );
    }

    /**
     * Get User ID
     * @return int
     */
    public function getID() {
        return $this->id;
    }

    /**
     * Get User Name
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Change User Name
     * @param string $name New User Name
     * @return boolean
     */
    public function setName(string $name) {
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $db->execute(
            "UPDATE USERS SET name = :name WHERE id = :id;",
            [":name" => $name, ":id" => $this->id]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        $this->name = $name;
        return true;
    }

    /**
     * Get User Email
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Change User Email
     * @param string $email New User Email
     * @return boolean
     */
    public function setEmail(string $email) {
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $db->execute(
            "UPDATE USERS SET email = :email WHERE id = :id;",
            [":email" => $email, ":id" => $this->id]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        $this->email = $email;
        return true;
    }

    /**
     * Get User Access Level
     * @return int
     */
    public function getAccessLevel() {
        return $this->level;
    }

    /**
     * Change User Access Level
     * @param int $leve New User Access Level
     * @return boolean
     */
    public function setAccessLevel(int $level) {
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $db->execute(
            "UPDATE USERS SET level = :level WHERE id = :id;",
            [":level" => $level, ":id" => $this->id]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        $this->level = $level;
        return true;
    }

    /**
     * Verifies User Password
     * @return bool
     */
    public function verifyPassword(string $password) {
        return password_verify($password, $this->hash);
    }

    /**
     * Change User Password
     * @param string $password New User Password
     * @return boolean
     */
    public function setPassword(string $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $db->execute(
            "UPDATE USERS SET hash = :hash WHERE id = :id;",
            [":hash" => $hash, ":id" => $this->id]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        $this->hash = $hash;
        return true;
    }

    /**
     * Deletes User from Database
     * @return bool
     */
    public function delete() {
        $db = new Database(AUTH_MODULE_DB);
        $db->beginTransaction();
        $db->execute(
            "DELETE FROM USERS WHERE id = :id;",
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
