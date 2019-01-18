<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat;

use NumberToWords\NumberToWords;

class Operations {

    public static function formatAmount($amount, $decimals = 2) {
        return number_format($amount, $decimals, '.', '');
    }

    public static function getAmountInWords($amount, $currency = '') {
        $formatedNumber = self::formatAmount($amount);

        $parts = explode('.', $formatedNumber);
        $intPart = (int)$parts[0];
        $decimalPart = $parts[1];
        $numberTransformer = (new NumberToWords())->getNumberTransformer('es');
        $t1 = mb_strtoupper($numberTransformer->toWords($intPart));
        $t2 = $t1 . " Y $decimalPart/100";
        return $currency ? "$t2 $currency" : $t2;
    }

    /**
     * 
     * @param float $amount
     * @param array $items
     * @return float
     */
    public static function applyAllowancesAndCharges($amount, array $items = []) {
        if (!$amount) {
            return 0;
        }
        $totalAllowances = 0;
        $totalCharges = 0;
        foreach ($items as $item) {
            $isCharge = $item['isCharge'];
            $k = $item['multiplierFactor'];
            $r = $amount * $k;
            if ($isCharge) {
                $totalCharges += $r;
            } else {
                $totalAllowances += $r;
            }
        }
        return $amount - $totalAllowances + $totalCharges;
    }

    public static function getTotalAllowanceCharge($amount, array $items, $isCharge) {
        $total = 0;
        foreach ($items as $item) {
            $k = $item['multiplierFactor'];
            if ($item['isCharge'] == $isCharge) {
                $total += $amount * $k;
            }
        }
        return $total;
    }

    public static function getTotalCharges($amount, array $items) {
        return self::getTotalAllowanceCharge($amount, $items, true);
    }

    public static function getTotalAllowances($amount, array $items) {
        return self::getTotalAllowanceCharge($amount, $items, false);
    }

    /**
     * Calcular IGV
     * @param float $baseAmount
     * @return float
     */
    public static function calcIGV($baseAmount) {
        return $baseAmount * SunatVars::IGV;
    }

    /**
     * Calcular ISC
     * @IMP
     * @return float
     */
    public static function calcISC() {
        return 0;
    }

    /**
     * Calcular IVAP
     * @IMP
     * @return float
     */
    public static function calcIVAP() {
        return 0;
    }

    /**
     * Aplica IGV?
     * 
     * @param string $igvAffectCode @CAT7
     * @return boolean
     */
    public static function isIGVAffected($igvAffectCode) {
        return ($igvAffectCode === Catalogo::CAT7_GRA_IGV);
    }

}
