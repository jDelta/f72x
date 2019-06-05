<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Exception;

use Exception;

class InvalidInputException extends Exception {

    private $errors;

    public function __construct($message, array $errors = []) {
        $this->message = $message;
        $this->errors = $errors;
        parent::__construct($message);
    }

    public function getErrors() {
        return $this->errors;
    }

}
