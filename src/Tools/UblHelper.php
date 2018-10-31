<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Tools;

use F72X\Sunat\Catalogo;
use F72X\Sunat\Document\SunatInvoice;
use F72X\UblComponent\TaxTotal;
use F72X\UblComponent\TaxSubTotal;
use F72X\UblComponent\TaxCategory;
use F72X\UblComponent\TaxScheme;
use F72X\UblComponent\InvoiceLine;
use F72X\UblComponent\AllowanceCharge;

class UblHelper {

    /**
     * 
     * @param SunatInvoice|InvoiceLine $target
     * @param array $allowancesCharges
     * @param float $baseAmount
     * @param string $currencyCode
     */
    public static function addAllowancesCharges($target, array $allowancesCharges, $baseAmount, $currencyCode) {
        foreach ($allowancesCharges as $item) {
            $k = $item['multiplierFactor'];
            $amount = $baseAmount * $k;
            $chargeIndicator = $item['isCharge'] ? 'true' : 'false';
            self::addAllowanceCharge($target, $currencyCode, $chargeIndicator, $item['reasonCode'], $item['multiplierFactor'], $amount, $baseAmount);
        }
    }

    /**
     * 
     * @param SunatInvoice|InvoiceLine $target
     * @param string $currencyID
     * @param string $ChargeIndicator
     * @param string $AllowanceChargeReasonCode
     * @param float $Amount
     * @param float $BaseAmount
     */
    public static function addAllowanceCharge($target, $currencyID, $ChargeIndicator, $AllowanceChargeReasonCode, $MultiplierFactorNumeric, $Amount, $BaseAmount) {
        $AllowanceCharge = new AllowanceCharge();
        $AllowanceCharge
                ->setCurrencyID($currencyID)
                ->setChargeIndicator($ChargeIndicator)
                ->setAllowanceChargeReasonCode($AllowanceChargeReasonCode)
                ->setMultiplierFactorNumeric($MultiplierFactorNumeric)
                ->setAmount($Amount)
                ->setBaseAmount($BaseAmount);
        // Add AllowanceCharge
        $target
                ->addAllowanceCharge($AllowanceCharge);
    }

    public static function addTaxSubtotal(TaxTotal $TaxTotal, $currencyID, $taxAmount, $taxableAmount, $taxTypeCode) {
        // XML nodes
        $TaxSubTotal = new TaxSubTotal();
        $TaxCategory = new TaxCategory();
        $TaxCategory->setElementAttributes('ID', [
            'schemeID'         => 'UN/ECE 5305',
            'schemeName'       => 'Tax Category Identifier',
            'schemeAgencyName' => 'United Nations Economic Commission for Europe'
        ]);
        $TaxScheme = new TaxScheme();
        $TaxScheme->setElementAttributes('ID', [
            'schemeID'       => 'UN/ECE 5153',
            'schemeAgencyID' => '6']);

        $cat5Item = Catalogo::getCatItem(5, $taxTypeCode);

        $TaxSubTotal
                ->setCurrencyID($currencyID)
                ->setTaxAmount($taxAmount)
                ->setTaxableAmount($taxableAmount)
                ->setTaxCategory($TaxCategory
                        ->setID($cat5Item['categoria'])
                        ->setTaxScheme($TaxScheme
                                ->setID($taxTypeCode)
                                ->setName($cat5Item['name'])
                                ->setTaxTypeCode($cat5Item['UN_ECE_5153'])));
        // Add item
        $TaxTotal->addTaxSubTotal($TaxSubTotal);
    }

}
