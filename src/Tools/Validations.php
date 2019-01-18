<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Tools;

class Validations {

    /**
     * Checks if the provided value is a DNI.
     * @param string $value
     * @return boolean
     */
    public static function isDni($value) {
        return is_numeric($value) && (strlen($value) == 8);
    }

    /**
     * Checks if the provided value is a RUC.
     * @param string $value
     * @return boolean
     */
    public static function isRuc($value) {
        return is_numeric($value) && (strlen($value) == 11);
    }

    /**
     * Checks if a value exist in an array.
     * @param mixed $needle The searched value.
     * @param array $haystack The array.
     * @param boolean $strict
     * If the third parameter *strict* is set to **false**
     * then the **isIn** function wont check the
     * *needle* in the *haystack*.
     * @return boolean
     */
    public static function isIn($needle, array $haystack, $strict = true) {
        return in_array($needle, $haystack, $strict);
    }

    public static function isArray($var) {
        return is_array($var);
    }

}
