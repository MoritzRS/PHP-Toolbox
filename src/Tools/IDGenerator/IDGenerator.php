<?php

namespace Tools\IDGenerator;

class IDGenerator {
    /**
     * Generation length
     * @var int
     */
    private int $length;

    /**
     * Generation mode
     * @var int
     */
    private int $mode;

    public function __construct(int $length = 16, int $mode = IDModes::Default) {
        $this->length = $length;
        $this->mode = $mode;
    }

    /**
     * Generates random string based on given character set
     * @param string $characters Character set
     * @return string
     */
    private function randomCharacters(string $characters) {
        $result = "";
        for ($i = 0; $i < $this->length; $i++) {
            $result .= $characters[random_int(0, strlen($characters) - 1)];
        };
        return $result;
    }

    /**
     * Generates a random ID string with hexadecimal character set
     * @return string
     */
    private function hexadecimal() {
        $characters = "0123456789abcdef";
        return $this->randomCharacters($characters);
    }

    /**
     * Generates a random ID string with decimal character set
     * @return string
     */
    private function decimal() {
        $characters = "0123456789";
        return $this->randomCharacters($characters);
    }

    /**
     * Generates a random ID string with alphabetical character set
     * @return string
     */
    private function alphabetical() {
        $characters = "abcdefghijklmnopqrstuvwxyz";
        return $this->randomCharacters($characters);
    }

    /**
     * Generates a random ID string with alphanumerical character set
     * @return string
     */
    private function alphanumerical() {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        return $this->randomCharacters($characters);
    }

    /**
     * Generates a random id
     * @return string
     */
    public function next() {
        switch ($this->mode) {
            case IDModes::Decimal:
                return $this->decimal();

            case IDModes::Alphabetical:
                return $this->alphabetical();

            case IDModes::Alphanumerical:
                return $this->alphanumerical();

            default:
                return $this->hexadecimal();
        }
    }
}
