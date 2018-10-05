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
use F72X\UblComponent\OrderReference;
use F72X\UblComponent\Party;
use F72X\UblComponent\PartyIdentification;
use F72X\UblComponent\PartyName;
use F72X\UblComponent\AccountingSupplierParty;
use F72X\UblComponent\AccountingCustomerParty;
use F72X\UblComponent\PartyLegalEntity;
use F72X\UblComponent\TaxTotal;
use F72X\UblComponent\TaxSubTotal;
use F72X\UblComponent\TaxCategory;
use F72X\UblComponent\TaxScheme;
use F72X\UblComponent\LegalMonetaryTotal;
use F72X\UblComponent\InvoiceLine;
use F72X\UblComponent\AllowanceCharge;
use F72X\UblComponent\PricingReference;
use F72X\UblComponent\AlternativeConditionPrice;
use F72X\UblComponent\Item;
use F72X\UblComponent\SellersItemIdentification;
use F72X\UblComponent\CommodityClassification;
use F72X\UblComponent\Price;

class UblHelper {

    /**
     * 
     * @param SunatDocument|InvoiceLine $target
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
        $TaxTotal->addTaxSubTotal($TaxSubTotal);
    }

}
