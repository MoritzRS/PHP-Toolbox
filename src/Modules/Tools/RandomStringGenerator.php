<?php

namespace Modules\Tools;

class RandomStringGenerator {

    private string $characters;

    public function __construct($characters = "abcdefghijklmnopqrstuvwxyz0123456789") {
        $this->characters = $characters;
    }

    public function next(int $length) {
        $result = "";
        for ($i = 0; $i < $length; $i++) {
            $result .= $this->characters[random_int(0, strlen($this->characters) - 1)];
        };
        return $result;
    }
}
