<?php

namespace Tools\Template;

class Template {

    /**
     * Template Object
     * @var mixed
     */
    private $template;

    /**
     * Creates a new TemplateValidator Object
     * @param mixed $template
     */
    public function __construct($template) {
        $this->template = $template;
    }

    /**
     * Creates template from provided json file
     * @param string $file Path to JSON File
     * @return Template
     */
    public static function fromJSON(string $file) {
        $json = file_get_contents($file);
        $parsed = json_decode($json) ?? "";
        return new self($parsed);
    }

    /**
     * Validates primitve types
     * @param string $template Template
     * @param mixed $target Target
     * @return bool True if the target is valid
     */
    private function validatePrimitives(string $template, $target) {

        // type: mixed
        if ($template == "mixed") return true;

        // type: string
        if ($template == "string") return is_string($target);

        // type: integer
        if ($template == "integer") return is_int($target);

        // type: float
        if ($template == "float") return is_float($target);

        // type: number
        if ($template == "number") return is_int($target) || is_float($target);

        // type: numeric
        if ($template == "numeric") return is_numeric($target);

        // type: boolean
        if ($template == "boolean") return is_bool($target);

        return false;
    }

    /**
     * Validates array types
     * @param array $template Template
     * @param array $target Target
     * @return bool True if the target is valid
     */
    private function validateArray(array $template, array $target): bool {
        if (!isset($template[0])) return false;
        $template = $template[0];

        foreach ($target as $value) {
            if (!$this->validateMain($template, $value)) return false;
        }
        return true;
    }

    /**
     * Validates object types
     * @param object $template Template
     * @param object $target Target
     * @return bool True if the target is valid
     */
    private function validateObject(object $template, object $target): bool {
        foreach ($template as $key => $value) {
            if (!$this->validateMain($template->$key, $target->$key)) return false;
        }
        return true;
    }

    /**
     * Validates a target against a template
     * @param mixed $template Template
     * @param mixed $target Target
     * @return bool True if the target is valid
     */
    private function validateMain($template, $target): bool {
        // type: object
        if (is_object($template) && !is_object($target)) return false;
        if (is_object($template) && is_object($target)) return $this->validateObject($template, $target);

        // type: array
        if (is_array($template) && !is_array($target)) return false;
        if (is_array($template) && is_array($target)) return $this->validateArray($template, $target);

        // type: primitive
        return $this->validatePrimitives($template, $target);
    }

    /**
     * Validates the target
     * @param mixed $target Target
     * @return boolean
     */
    public function validate($target) {
        return $this->validateMain($this->template, $target);
    }
}
