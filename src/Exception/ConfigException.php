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

class ConfigException extends Exception {

    protected $message = 'Error de configuración del modulo F72X';

}
