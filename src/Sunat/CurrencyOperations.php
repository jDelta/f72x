<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat;

use NumberToWords\NumberToWords;

class CurrencyOperations {

    public static function formatAmount($value, $decimals = 2) {
        return number_format($value, $decimals, '.', '');
    }

    public static function getAmountInWords($amount) {
        $formatedNumber = self::formatAmount($amount, 2);
        
        $parts = explode('.', $formatedNumber);
        $intPart = $parts[0];
        $decimalPart = $parts[1];

        $numberTransformer = (new NumberToWords())->getNumberTransformer('es');
        $t1 = strtoupper($numberTransformer->toWords($intPart));
        $t2 = str_replace('ú', 'Ú', $t1);
        $t3 = str_replace('ó', 'Ó', $t2);
        
        return $t3 . " Y $decimalPart/100";
    }
}
