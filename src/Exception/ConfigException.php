<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Exception;

use Exception;

/**
 * Model Load Exception
 *
 * This Exception is thrown when a model can't be loaded withe specified ID.
 */
class ConfigException extends Exception {

    protected $message = 'Olvidaste configurar el Modulo F72X usa \F72X\F72::init($config)';

}
