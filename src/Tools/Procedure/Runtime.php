<?php

namespace Tools\Procedure;

use Tools\HTTP\HTTPCodes;

class Runtime {
    /**
     * Registered procedures
     * @var array<string,Procedure>
     */
    private array $procedures;

    public function __construct(array $procedures = []) {
        $this->procedures = $procedures;
    }

    /**
     * Registers new procedures in the runtime
     * @param array<string,Procedure> $procedures
     */
    public function register(array $procedures = []) {
        foreach ($procedures as $key => $value) $this->procedures[$key] = $value;
    }

    /**
     * Evaluates a targeted procedure and returns the result
     * @param string $procedure Targeted Procedure
     * @param mixed $payload Procedure payload
     * @param mixed $options Procedure options
     */
    public function evaluate(string $procedure, $payload = null, $options = null) {
        if (!isset($this->procedures[$procedure])) {
            http_response_code(HTTPCodes::NotFound);
            return false;
        }

        return $this->procedures[$procedure]->execute($payload ?? (object)[], $options ?? (object)[]);
    }
}
