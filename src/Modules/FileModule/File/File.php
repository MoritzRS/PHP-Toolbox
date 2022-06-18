<?php

namespace Modules\FileModule\File;

use Tools\Database\Database;
use Tools\IDGenerator\IDGenerator;
use Tools\IDGenerator\IDModes;

class File {
    private string $id;
    private string $name;
    private string $path;
    private string $extension;

    private function __construct(string $id, string $name, string $path, string $extension) {
        $this->id = $id;
        $this->name = $name;
        $this->path = $path;
        $this->extension = $extension;
    }

    /**
     * Gets File by ID
     * @param string $id File ID
     * @return File|false
     */
    public static function getByID(string $id) {
        $db = new Database(FILE_MODULE_DB);
        $db->beginTransaction();

        $data = $db->fetchSingle(
            "SELECT id, name, path, extension FROM FILES WHERE id = :id;",
            [":id" => $id]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        return new self($data["id"], $data["name"], $data["path"], $data["extension"]);
    }

    /**
     * Get all Files
     * @return File[]|false
     */
    public static function getAll() {
        $db = new Database(FILE_MODULE_DB);
        $db->beginTransaction();

        $data = $db->fetch("SELECT id, name, path, extension FROM FILES;");

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        $files = array_map(function ($entry) {
            return new self($entry["id"], $entry["name"], $entry["path"], $entry["extension"]);
        }, $data);

        usort($files, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });
        return $files;
    }

    /**
     * Get all Files of given Extensions
     * @param string[] $extensions File Extensions
     * @return File[]|false
     */
    public static function getByExtensions(array $extensions) {
        $totalFiles = [];
        $db = new Database(FILE_MODULE_DB);
        $db->beginTransaction();

        foreach ($extensions as $extension) {
            $data = $db->fetch(
                "SELECT id, name, path, extension FROM FILES WHERE extension = :ext;",
                [":ext" => strtolower($extension)]
            );

            if ($db->hasException()) {
                $db->rollBack();
                return false;
            }

            $db->commit();
            $files = array_map(function ($entry) {
                return new self($entry["id"], $entry["name"], $entry["path"], $entry["extension"]);
            }, $data);
            array_push($totalFiles, ...$files);
        }

        usort($totalFiles, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });
        return $totalFiles;
    }

    /**
     * Uploads, Registers and Stores a File from $_FILES
     * @param array $file File
     * @return File|false
     */
    public static function upload(array $file) {
        if (!$file["tmp_name"]) return false;
        if (!is_uploaded_file($file["tmp_name"])) return false;
        if (filesize($file["tmp_name"]) > FILE_MODULE_MAX_SIZE) return false;

        $idGenerator = new IDGenerator(16, IDModes::Alphanumerical);
        $id = $idGenerator->next();
        while (self::getByID($id) !== false) {
            $id = $idGenerator->next();
        }

        $name = $file["name"];
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $directory = FILE_MODULE_FILE_PATH . "/" . $extension;
        $path = $directory . "/" . $id . "." . $extension;

        $db = new Database(FILE_MODULE_DB);
        $db->beginTransaction();

        $db->execute(
            "INSERT INTO FILES(id, name, path, extension) VALUES(:id, :name, :path, :extension);",
            [
                ":id" => $id,
                ":path" => $path,
                ":name" => $name,
                ":extension" => $extension
            ]
        );

        if ($db->hasException()) {
            $db->rollBack();
            return false;
        }

        if (!is_dir($directory)) mkdir($directory, 0777, true);
        $moved = move_uploaded_file($file["tmp_name"], $path);

        if (!$moved) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        return new self($id, $name, $path, $extension);
    }

    /**
     * Gets File ID
     * @return string
     */
    public function getID() {
        return $this->id;
    }

    /**
     * Gets File Path
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Gets File Name
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets File extension
     * @return string
     */
    public function getExtension() {
        return $this->extension;
    }


    /**
     * Deletes File
     * @return bool
     */
    public function delete() {
        $db = new Database(FILE_MODULE_DB);
        $db->beginTransaction();

        $db->execute("DELETE FROM FILES WHERE id = :id;", [":id" => $this->id]);
        $unlinked = unlink($this->path);

        if ($db->hasException() || !$unlinked) {
            $db->rollBack();
            return false;
        }

        $db->commit();
        return true;
    }
}
