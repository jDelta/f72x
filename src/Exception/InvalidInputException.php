<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Exception;

use Exception;

class InvalidInputException extends Exception {

    private $errors = [];
    protected $message = 'Verifica los tus datos provistos usa getErrors para obtener los errores detectados';

    public function __construct(array $errors) {
        $this->errors = $errors;
    }

    public function getErrors() {
        return $this->errors;
    }

}
