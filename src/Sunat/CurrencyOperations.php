<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat;

class CurrencyOperations {

    public static function formatAmount($value, $decimals = 2) {
        return number_format($value, $decimals, '.', '');
    }

}
