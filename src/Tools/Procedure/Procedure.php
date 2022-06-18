<?php

namespace Tools\Procedure;

use Tools\HTTP\HTTPCodes;
use Tools\Template\Template;

class Procedure {

    /**
     * Generates the template.
     * Used for dynamic generation on usage and inheritance
     * @return Template
     */
    protected function template() {
        return new Template("mixed");
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
        if (!$this->template()->validate($payload)) {
            http_response_code(HTTPCodes::BadRequest);
            return false;
        }
        return $this->process($payload);
    }
}
