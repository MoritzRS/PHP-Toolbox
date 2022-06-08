<?php

namespace Tools\Procedure;

use Tools\HTTP\HTTPCodes;
use Tools\Template\Template;

class Procedure {
    /**
     * Payload Template for validation
     * @var Template
     */
    protected $template;

    public function __construct() {
        $this->template = new Template("mixed");
    }

    /**
     * Processes payload and returns result
     * @param mixed $payload
     * @return mixed
     */
    protected function process($payload) {
        return $payload;
    }

    /**
     * Executes the procedure
     * @param mixed $payload Parameter payload
     * @param mixed $options Calling options
     * @return mixed
     */
    public function execute($payload, $options = []) {
        if (!$this->template->validate($payload)) {
            http_response_code(HTTPCodes::BadRequest);
            return false;
        }
        return $this->process($payload);
    }
}
