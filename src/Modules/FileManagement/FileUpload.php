<?php

namespace Modules\FileManagement;

class FileUpload {
    private string $path;
    private string $dirname;
    private string $filename;
    private string $extension;
    private int $size;
    private string $tempPath;

    public function __construct(array $file, bool $unique = false) {
        if (!$file["tmp_name"]) return false;
        if (!is_uploaded_file($file["tmp_name"])) return false;
        $this->tempPath = $file["tmp_name"];
        $this->size = filesize($file["tmp_name"]);
        $this->extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $this->dirname = FILES_DIR . "/" . $this->extension;
        $name = hash_file("md5", $file["tmp_name"]);
        if ($unique) $name .= date("_Ymd_His");
        $this->filename = $name . "." . $this->extension;
        $this->path = $this->dirname . "/" . $this->filename;
    }

    /**
     * Returns the full path where the file will be saved
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Returns the directory where the file will be saved
     */
    public function getDirname() {
        return $this->dirname;
    }

    /**
     * Returns the filename where the file will be saved
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * Returns the file extension
     */
    public function getExtension() {
        return $this->extension;
    }

    /**
     * Returns the filesize in bytes
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * Saves the file
     */
    public function save() {
        if (!is_dir($this->dirname)) mkdir($this->dirname, 0777, true);
        return move_uploaded_file($this->tempPath, $this->path);
    }
}
