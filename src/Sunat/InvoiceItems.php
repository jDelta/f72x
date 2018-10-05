<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat;

use F72X\Tools\XMatrix;

class InvoiceItems extends XMatrix {
    
    const COL_PRODUCT_CODE          = 0;
    const COL_UNPSC                 = 1;
    const COL_UNIT_CODE             = 2;
    const COL_QUANTITY              = 3;
    const COL_DESCRIPTION           = 4;
    const COL_CURRENCY_CODE         = 5;
    const COL_UNIT_VALUE            = 6;
    const COL_UNIT_BILLABLE_VALUE   = 7;
    const COL_UNIT_TAXED_VALUE      = 8;
    // Códigos de catálogos predefinidos
    const COL_PRICE_TYPE            = 9;
    const COL_TAX_TYPE              = 10;
    const COL_IGV_AFFECTATION       = 11;

    const COL_ITEM_VALUE            = 12;
    const COL_ITEM_BILLABLE_VALUE   = 13;
    // Cargos y descuentos
    const COL_ALLOWANCES            = 14;
    const COL_ALLOWANCES_AMOUNT     = 15;
    const COL_CHARGES               = 16;
    const COL_CHARGES_AMOUNT        = 17;

    const COL_ITEM_TAXABLE_AMOUNT   = 18;

    const COL_IGV                   = 19;
    const COL_ITEM_PAYABLE_AMOUNT   = 20;

    protected $columns = [
        'COL_PRODUCT_CODE',
        'COL_UNPSC',
        'COL_UNIT_CODE',
        'COL_QUANTITY',
        '============COL_DESCRIPTION============',
        'COL_CURRENCY_CODE',
        'COL_UNIT_VALUE',
        'COL_UNIT_BILLABLE_VALUE',
        'COL_UNIT_TAXED_VALUE',
        'COL_PRICE_TYPE[CAT#16]',
        'COL_TAX_TYPE[CAT#5]',
        'COL_IGV_AFFECTATION[CAT#7]',
        'COL_ITEM_VALUE [COL_UNIT_VALUE*COL_QUANTITY]',
        'COL_ITEM_BILLABLE_VALUE [COL_UNIT_BILLABLE_VALUE*COL_QUANTITY]',
        'COL_ALLOWANCES',
        'COL_ALLOWANCES_AMOUNT [COL_ITEM_BILLABLE_VALUE*k]',
        'COL_CHARGES',
        'COL_CHARGES_AMOUNT [COL_ITEM_BILLABLE_VALUE*k]',
        'COL_ITEM_TAXABLE_AMOUNT(operacion_gravada) [COL_ITEM_BILLABLE_VALUE-descuentos+cargos]',
        'COL_IGV(operacion_gravada) [COL_ITEM_BILLABLE_VALUE*IGV_PERCENT]',
        'COL_ITEM_PAYABLE_AMOUNT [base_imponible+IGV]'
    ];

    public function populate($items,  $currencyCode) {
        foreach ($items as $idx => $item) {
            $allowances     = isset($item['allowances']) ? $item['allowances'] : [];
            $charges        = isset($item['charges']) ? $item['charges'] : [];
            $igvAffectCode  = $item['igvAffectationCode'];
            $priceType      = $item['priceType'];   // Tipo de precio
            $grossUnitValue = $item['unitValue'];
            $igvIncluded    = $item['igvIncluded'];
            
            $unitValue         = $this->calcUnitValue($igvAffectCode, $grossUnitValue, $igvIncluded);      // Valor unitario
            $unitTaxedValue    = $this->calcUnitTaxedValue($igvAffectCode, $grossUnitValue, $igvIncluded); // Valor unitario incluyendo impuestos si son aplicables
            $unitBillableValue = $this->calcUnitBillableValue($unitValue, $priceType);                     // Valor unitario facturable
            $quantity          = $item['quantity'];  // Cantidad

            $itemValue            = $unitValue * $quantity;         // Valor de item
            $itemBillableValue    = $unitBillableValue * $quantity; // Valor de item
            $itemAllowancesAmount = Operations::getTotalAllowanceCharge($itemBillableValue, $allowances); // Descuentos de item
            $itemChargesAmount    = Operations::getTotalAllowanceCharge($itemValue, $charges);            // Cargos de item
            $itemTaxableAmount    = $this->calcItemTaxableAmount($itemValue, $priceType, $allowances, $charges);         // Valor de venta del ítem = (Valor del item - Descuentos + Cargos), 0 si el valor del item es referencial!
            $igvAmount            = $this->calcIgvAmount($igvAffectCode, $itemTaxableAmount); // Afectación al IGV por item
            
            $itemIgvTaxed         = $itemBillableValue + $igvAmount;
            
            $this->set(self::COL_PRODUCT_CODE,        $idx, $item['productCode']);
            $this->set(self::COL_UNPSC,               $idx, $item['sunatProductCode']);
            $this->set(self::COL_UNIT_CODE,           $idx, $item['unitCode']);
            $this->set(self::COL_QUANTITY,            $idx, $quantity);
            $this->set(self::COL_DESCRIPTION,         $idx, $item['description']);
            $this->set(self::COL_CURRENCY_CODE,       $idx, $currencyCode);
            // Códigos de catálogos predefinidos
            $this->set(self::COL_PRICE_TYPE,          $idx, $priceType);
            $this->set(self::COL_TAX_TYPE,            $idx, $item['taxType']);
            $this->set(self::COL_IGV_AFFECTATION,     $idx, $item['igvAffectationCode']);

            $this->set(self::COL_UNIT_VALUE,          $idx, $unitValue);
            $this->set(self::COL_UNIT_BILLABLE_VALUE, $idx, $unitBillableValue);
            $this->set(self::COL_UNIT_TAXED_VALUE,    $idx, $unitTaxedValue);
            $this->set(self::COL_ITEM_VALUE,          $idx, $itemValue);
            $this->set(self::COL_ITEM_BILLABLE_VALUE, $idx, $itemBillableValue);
            $this->set(self::COL_ALLOWANCES,          $idx, $allowances);
            $this->set(self::COL_ALLOWANCES_AMOUNT,   $idx, $itemAllowancesAmount);
            $this->set(self::COL_CHARGES,             $idx, $charges);
            $this->set(self::COL_CHARGES_AMOUNT,      $idx, $itemChargesAmount);
            $this->set(self::COL_ITEM_TAXABLE_AMOUNT, $idx, $itemTaxableAmount);
            $this->set(self::COL_IGV,                 $idx, $igvAmount);
            $this->set(self::COL_ITEM_PAYABLE_AMOUNT, $idx, $itemIgvTaxed);
        }
    }

    /**
     * Valor unitario: se extrae el IGV si el valor es afectado por este y si se
     * encuentra incluido en el monto que se recibe como segundo parametro.
     * 
     * @param string $igvAffectCode
     * @param float $baseAmount
     * @param boolean $igvIncluded
     * @return float
     */
    private function calcUnitValue($igvAffectCode, $baseAmount, $igvIncluded) {
        $amount = $baseAmount;
        if (Operations::isIGVAffected($igvAffectCode) && $igvIncluded) {
            $amount = $baseAmount / (1 + SunatVars::IGV);
        }
        return $amount;
    }

    /**
     * Valor unitario pagable: se aplica el IGV si este es aplicable y si aún no
     * ha sido incluido en el monto que se recibe como segundo parametro.
     * 
     * @param string $igvAffectCode
     * @param float $baseAmount
     * @param boolean $igvIncluded
     * @return float
     */
    private function calcUnitTaxedValue($igvAffectCode, $baseAmount, $igvIncluded) {
        $amount = $baseAmount;
        if (Operations::isIGVAffected($igvAffectCode) && !$igvIncluded) {
            $amount = $baseAmount * (1 + SunatVars::IGV);
        }
        return $amount;
    }

    /**
     * 
     * Valor facturable
     * 
     * El valor que figurará en el comprobante como valor unitario a pagar
     * 
     * @param float $baseAmount
     * @param boolean $priceType
     * @return float
     */
    private function calcUnitBillableValue($baseAmount, $priceType) {
        return ($priceType === Catalogo::CAT16_REF_VALUE) ? 0 : $baseAmount;
    }

    private function calcItemTaxableAmount($baseAmount, $priceType, $allowances, $charges) {
        // Valor de venta del ítem = (Valor del item - Descuentos + Cargos)
        if ($priceType === Catalogo::CAT16_UNITARY_PRICE) {
            $amount = Operations::applyAllowancesAndCharges($baseAmount, $allowances, $charges);
        } else {
            // 0 si el valor del item es referencial!
            $amount = 0;
        }
        return $amount;
    }

    /**
     * IGV: Calcula el monto del IGV, si este es aplicable.
     * @param string $igvAffectCode
     * @param float $baseAmount
     * @return float
     */
    private function calcIgvAmount($igvAffectCode, $baseAmount) {
        return Operations::isIGVAffected($igvAffectCode) ? Operations::calcIGV($baseAmount) : 0;
    }

    /**
     * Codigo de producto
     * 
     * @param int $rowIndex
     * @return string
     */
    public function getProductCode($rowIndex) {
        return $this->get(self::COL_PRODUCT_CODE, $rowIndex);
    }

    /**
     * United Nations Standard Products and Services Code
     * Codigo de producto SUNAT de acuerdo con UNSPSC v14_0801
     * @param int $rowIndex
     * @return string
     */
    public function getUNPSC($rowIndex) {
        return $this->get(self::COL_UNPSC, $rowIndex);
    }

    public function getUnitCode($rowIndex) {
        return $this->get(self::COL_UNIT_CODE, $rowIndex);
    }

    public function getQunatity($rowIndex) {
        return $this->get(self::COL_QUANTITY, $rowIndex);
    }

    public function getDescription($rowIndex) {
        return $this->get(self::COL_DESCRIPTION, $rowIndex);
    }

    public function getCurrencyCode($rowIndex) {
        return $this->get(self::COL_CURRENCY_CODE, $rowIndex);
    }

    public function getPriceTypeCode($rowIndex) {
        return $this->get(self::COL_PRICE_TYPE, $rowIndex);
    }

    public function getTaxTypeCode($rowIndex) {
        return $this->get(self::COL_TAX_TYPE, $rowIndex);
    }

    public function getIgvAffectationCode($rowIndex) {
        return $this->get(self::COL_IGV_AFFECTATION, $rowIndex);
    }

    public function getUnitValue($rowIndex) {
        return $this->get(self::COL_UNIT_VALUE, $rowIndex);
    }

    public function getUnitBillableValue($rowIndex) {
        return $this->get(self::COL_UNIT_BILLABLE_VALUE, $rowIndex);
    }

    public function getUnitTaxedValue($rowIndex) {
        return $this->get(self::COL_UNIT_TAXED_VALUE, $rowIndex);
    }

    public function getItemValue($rowIndex) {
        return $this->get(self::COL_ITEM_VALUE, $rowIndex);
    }

    public function getItemBillableValue($rowIndex) {
        return $this->get(self::COL_ITEM_BILLABLE_VALUE, $rowIndex);
    }

    public function getAllowances($rowIndex) {
        return $this->get(self::COL_ALLOWANCES, $rowIndex);
    }

    public function getAllowancesAmount($rowIndex) {
        return $this->get(self::COL_ALLOWANCES_AMOUNT, $rowIndex);
    }

    public function getCharges($rowIndex) {
        return $this->get(self::COL_CHARGES, $rowIndex);
    }

    public function getChargesAmount($rowIndex) {
        return $this->get(self::COL_CHARGES_AMOUNT, $rowIndex);
    }

    public function getTaxableAmount($rowIndex) {
        return $this->get(self::COL_ITEM_TAXABLE_AMOUNT, $rowIndex);
    }

    public function getIgv($rowIndex) {
        return $this->get(self::COL_IGV, $rowIndex);
    }

    public function getPayableAmount($rowIndex) {
        return $this->get(self::COL_ITEM_PAYABLE_AMOUNT, $rowIndex);
    }

    /**
     * Total operaciones gravadas
     * @return float
     */
    public function getTotalTaxableOperations() {
        return $this->getTotalOperations(Catalogo::CAT5_IGV, self::COL_ITEM_TAXABLE_AMOUNT);
    }

    /**
     * Total operaciones gratuitas
     * @return float
     */
    public function getTotalFreeOperations() {
        return $this->getTotalOperations(Catalogo::CAT5_GRA, self::COL_ITEM_VALUE);
    }

    /**
     * Total operaciones exoneradas
     * @return float
     */
    public function getTotalExemptedOperations() {
        return $this->getTotalOperations(Catalogo::CAT5_EXO, self::COL_ITEM_TAXABLE_AMOUNT);
    }

    /**
     * Total operaciones inafectas
     * @return float
     */
    public function getTotalUnaffectedOperations() {
        return $this->getTotalOperations(Catalogo::CAT5_INA, self::COL_ITEM_VALUE);
    }

    private function getTotalOperations($taxType, $columnIndex) {
        $total = 0;
        $data = $this->getMatrix();
        foreach ($data as $row) {
            $total += ($row[self::COL_TAX_TYPE] === $taxType) ? $row[$columnIndex] : 0;
        }
        return $total;
    }

    /**
     * Todos de los descuentos.
     * @return float
     */
    public function getTotalAllowances() {
        return $this->sum(self::COL_ALLOWANCES_AMOUNT);
    }

    /**
     * Todos de los descuentos.
     * @return float
     */
    public function getTotalCharges() {
        return $this->sum(self::COL_CHARGES_AMOUNT);
    }

    /**
     * Valor total
     * Suma los valores de cada item
     * = Cantidad x (Valor unitario | Valor fererencial)
     * @return float
     */
    public function getTotalValue() {
        return $this->sum(self::COL_ITEM_VALUE);
    }

    /**
     * Suma de valores de cada item sin incluir aquellos por los que no se cobra.
     * @return float
     */
    public function getTotalBillableValue() {
        return $this->sum(self::COL_ITEM_BILLABLE_VALUE);
    }

    /**
     * Total IGV
     * @return float
     */
    public function getTotalIgv() {
        return $this->sum(self::COL_IGV);
    }

    /**
     * Base imponible total lista para aplicar impuestos
     * @return float
     */
    public function getTotalTaxableAmount() {
        return $this->sum(self::COL_ITEM_TAXABLE_AMOUNT);
    }

    /**
     * Total de items
     * @return int
     */
    public function getCount() {
        return $this->countRows();
    }

}
