<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat;

class Operations {

    public static function formatAmount($amount, $decimals = 2) {
        return number_format($amount, $decimals, '.', '');
    }

    /**
     * 
     * @param float $amount
     * @param array $allowances
     * @param array $charges
     * @return float
     */
    public static function applyAllowancesAndCharges($amount, $allowances = [], $charges = []) {
        if (!$amount) {
            return 0;
        }
        $totalAllowances = 0;
        $totalCharges = 0;
        foreach ($allowances as $allowanceItem) {
            $k = $allowanceItem['multiplierFactor'];
            $totalAllowances += $amount * $k;
        }
        foreach ($charges as $chargeItem) {
            $k = $chargeItem['multiplierFactor'];
            $totalCharges += $amount * $k;
        }
        return $amount - $totalAllowances + $totalCharges;
    }

    public static function getTotalAllowanceCharge($amount, $items = []) {
        $total = 0;
        foreach ($items as $item) {
            $k = $item['multiplierFactor'];
            $total += $amount * $k;
        }
        return $total;
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
