<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat;

use F72X\Tools\XMatrix;

class DetailMatrix extends XMatrix {

    /** @CAT5 Tipo de impuesto*/
    const TAX_IGV   = '1000';
    const TAX_IVAP  = '1016';
    const TAX_ISC   = '2000';
    const TAX_EXP   = '9995';
    const TAX_GRA   = '9996';
    const TAX_EXO   = '9997';
    const TAX_INA   = '9998';
    const TAX_OTROS = '9999';
    
    const COL_PRODUCT_CODE          = 0;
    const COL_UNPSC                 = 1;
    const COL_UNIT_CODE             = 2;
    const COL_QUANTITY              = 3;
    const COL_DESCRIPTION           = 4;
    const COL_CURRENCY_CODE         = 5;
    const COL_UNIT_VALUE            = 6;
    const COL_UNIT_BILLABLE_VALUE   = 7;
    const COL_UNIT_PAYABLE_AMOUNT   = 8;
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
        'cod',
        'cod_sunat',
        'unidad',
        'cantidad',
        '============descripción============',
        'moneda',
        'valor_unitario',
        'valor_unitario_facturable',
        'precio_unitario',
        'tipo_precio[CAT#16]',
        'tipo_impuesto[CAT#5]',
        'afectación_igv[CAT#7]',
        'valor_item [valor_unitario*cantidad]',
        'valor_item_facturable [valor_unitario_facturable*cantidad]',
        'descuentos',
        'monto_descuentos [valor_item_facturable*k]',
        'cargos',
        'monto_cargos [valor_item_facturable*k]',
        'base_imponible(operacion_gravada) [valor_item_facturable-descuentos+cargos]',
        'IGV(operacion_gravada) [valor_item_facturable*0.18]',
        'precio_item [base_imponible+IGV]'
    ];

    public function populate($items,  $currencyCode) {
        foreach ($items as $idx => $item) {

            $cat7Item = Catalogo::getCatItem(7, $item['igvAffectationCode']);   // Catálogo 7 Tipo de afectación del IGV
            $priceType  = $item['priceTypeCode'];   // Tipo de precio
            
            $unitValue = $item['unitValue'];       // Valor unitario
            $igvIncluded = $item['igvIncluded'];
            $unitPayableAmount = $unitValue;
            if(isset($cat7Item['igv'])){
                // Aplica IGV
                if ($igvIncluded) {
                    $unitPayableAmount = $unitValue;
                    $unitValue = $unitPayableAmount / (1 + SunatVars::IGV);
                } else{
                    $unitPayableAmount = $unitValue * (1 + SunatVars::IGV);
                }
            }
            $unitBillableValue = ($priceType === SunatVars::CAT16_REF_VALUE) ? 0 : $unitValue; // Valor unitario pagable
            $quantity = $item['quantity'];  // Cantidad

            $itemValue  = $unitValue * $quantity;   // Valor de item
            $itemBillableValue  = $unitBillableValue * $quantity;   // Valor de item
            // Descuentos de item
            $itemAllowancesAmount = 0;
            $allowancesArray = isset($item['allowances']) ? $item['allowances'] : [];
            foreach ($allowancesArray as $allItem) {
                $multFactor = $allItem['multiplierFactor'];
                $amount = $itemBillableValue * $multFactor;
                $itemAllowancesAmount += $amount;
            }
            // Cargos de item
            $itemChargesAmount = 0;
            $chargesArray = isset($item['charges']) ? $item['charges'] : [];
            foreach ($chargesArray as $charItem) {
                $multFactor = $charItem['multiplierFactor'];
                $amount = $itemValue * $multFactor;
                $itemChargesAmount += $amount;
            }

            // Valor de venta del ítem = (Valor del item - Descuentos + Cargos)
            if ($priceType === SunatVars::CAT16_UNITARY_PRICE) {
                $itemTaxableAmount = $itemValue - $itemAllowancesAmount + $itemChargesAmount;
            } else {
                // 0 si el valor del item es referencial!
                $itemTaxableAmount = 0;
            }

            // Afectación al IGV por item
            $igvAmount = 0;
            // Aplica IGV
            if (isset($cat7Item['igv'])) {
                $igvAmount = $itemTaxableAmount * SunatVars::IGV;
            }
            
            $itemIgvTaxed = $itemBillableValue + $igvAmount;
            
            $this->set(self::COL_PRODUCT_CODE,          $idx, $item['productCode']);
            $this->set(self::COL_UNPSC,                 $idx, $item['sunatProductCode']);
            $this->set(self::COL_UNIT_CODE,             $idx, $item['unitCode']);
            $this->set(self::COL_QUANTITY,              $idx, $quantity);
            $this->set(self::COL_DESCRIPTION,           $idx, $item['description']);
            $this->set(self::COL_CURRENCY_CODE,         $idx, $currencyCode);
            // Códigos de catálogos predefinidos
            $this->set(self::COL_PRICE_TYPE,       $idx, $priceType);
            $this->set(self::COL_TAX_TYPE,         $idx, $item['taxTypeCode']);
            $this->set(self::COL_IGV_AFFECTATION,  $idx, $item['igvAffectationCode']);

            $this->set(self::COL_UNIT_VALUE,            $idx, $unitValue);
            $this->set(self::COL_UNIT_BILLABLE_VALUE,   $idx, $unitBillableValue);
            $this->set(self::COL_UNIT_PAYABLE_AMOUNT,   $idx, $unitPayableAmount);
            $this->set(self::COL_ITEM_VALUE,            $idx, $itemValue);
            $this->set(self::COL_ITEM_BILLABLE_VALUE,   $idx, $itemBillableValue);
            $this->set(self::COL_ALLOWANCES,            $idx, $allowancesArray);
            $this->set(self::COL_ALLOWANCES_AMOUNT,     $idx, $itemAllowancesAmount);
            $this->set(self::COL_CHARGES,               $idx, $chargesArray);
            $this->set(self::COL_CHARGES_AMOUNT,        $idx, $itemChargesAmount);
            $this->set(self::COL_ITEM_TAXABLE_AMOUNT,   $idx, $itemTaxableAmount);
            $this->set(self::COL_IGV,                   $idx, $igvAmount);
            $this->set(self::COL_ITEM_PAYABLE_AMOUNT,   $idx, $itemIgvTaxed);
        }
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

    public function getUnitPayableAmount($rowIndex) {
        return $this->get(self::COL_UNIT_PAYABLE_AMOUNT, $rowIndex);
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
     * Retorna el total de las operaciones gravadas
     * @return float
     */
    public function getTotalTaxableOperations() {
        $total = 0;
        $data = $this->getMatrix();
        foreach ($data as $row) {
            $total += ($row[self::COL_TAX_TYPE] === self::TAX_IGV) ? $row[self::COL_ITEM_TAXABLE_AMOUNT] : 0;
        }
        return $total;
    }

    /**
     * Total de las operaciones exoneradas
     * @return float
     */
    public function getTotalExcemptedOperations() {
        $total = 0;
        $data = $this->getMatrix();
        foreach ($data as $row) {
            $total += ($row[self::COL_TAX_TYPE] === self::TAX_EXO) ? $row[self::COL_ITEM_TAXABLE_AMOUNT] : 0;
        }
        return $total;
    }
    /**
     * Total de las operaciones inafectas
     * @return float
     */
    public function getTotalUnaffectedOperations() {
        $total = 0;
        $data = $this->getMatrix();
        foreach ($data as $row) {
            $total += ($row[self::COL_TAX_TYPE] === self::TAX_INA) ? $row[self::COL_ITEM_VALUE] : 0;
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
    public function getTotalPayableValue() {
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
}
