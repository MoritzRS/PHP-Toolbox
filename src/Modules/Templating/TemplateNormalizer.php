<?php

namespace Modules\Templating;

class TemplateNormalizer {
    /**
     * Template Object
     * @var mixed
     */
    private $template;

    /**
     * Wether or not to use strict mode
     * @var bool
     */
    private $strict = false;

    /**
     * Creates a new TemplateValidator Object
     * @param mixed $template
     * @param bool $strict
     */
    public function __construct($template, bool $strict = false) {
        $this->template = $template;
        $this->strict = $strict;
    }

    /**
     * Creates template from provided json file
     * @param string $file Path to JSON File
     * @return TemplateNormalizer
     */
    public static function fromJSON(string $file, bool $strict = false) {
        $json = file_get_contents($file);
        $parsed = json_decode($json) ?? "";
        return new self($parsed, $strict);
    }

    /**
     * Normalizes primitive types
     * @param mixed $template Template
     * @param mixed $target Target
     * @return mixed normalized target
     */
    private function normalizePrimitive($template, $target) {
        if (!isset($target) || $target === null) return $template;
        if (gettype($target) !== gettype($template)) return $template;
        return $target;
    }

    /**
     * Normalizes arrays
     * @param array $template Template
     * @param array $target Target
     * @return array normalized target
     */
    private function normalizeArray(array $template, array $target) {
        if (!isset($template[0])) return $target;
        $normalized = $this->strict ? [] : $target;
        $template = $template[0];
        foreach ($target as $key => $_) {
            $normalized[$key] = $this->normalizeMain($template, $target[$key]);
        }
        return $normalized;
    }

    /**
     * Normalizes objects
     * @param object $template Template
     * @param object $target Target
     * @return object normalized target
     */
    private function normalizeObject(object $template, object $target) {
        $normalized = $this->strict ? (object)[] : $target;
        foreach ($template as $key => $_) {
            if (!isset($target->$key)) $normalized->$key = $template->$key;
            $normalized->$key = $this->normalizeMain($template->$key, $target->$key);
        }
        return $normalized;
    }

    /**
     * Normalizes a target using a template
     * @param mixed $template Template
     * @param mixed $target Target
     * @return mixed normalized target
     */
    private function normalizeMain($template, $target) {
        if (!isset($target)) return $template;

        // type: object
        if (is_object($template) && !is_object($target)) return $template;
        if (is_object($template) && is_object($target)) return $this->normalizeObject($template, $target);

        // type: array
        if (is_array($template) && !is_array($target)) return $template;
        if (is_array($template) && is_array($target)) return $this->normalizeArray($template, $target);

        // type: primitive
        return $this->normalizePrimitive($template, $target);
    }

    /**
     * Normalizes the target
     * @param mixed $target Target
     * @return boolean
     */
    public function normalize($target) {
        return $this->normalizeMain($this->template, $target);
    }
}
