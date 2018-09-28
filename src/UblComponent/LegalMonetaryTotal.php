<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\UblComponent;

use F72X\Sunat\CurrencyOperations;
use Sabre\Xml\Writer;

class LegalMonetaryTotal extends BaseComponent {

    const DECIMALS = 2;

    protected $currencyID;
    protected $LineExtensionAmount;
    protected $TaxInclusiveAmount;
    protected $AllowanceTotalAmount;
    protected $PayableAmount;
    protected $validations = [
        'currencyID',
        'LineExtensionAmount',
        'TaxInclusiveAmount',
        'AllowanceTotalAmount',
        'PayableAmount'
    ];
    function xmlSerialize(Writer $writer) {
        $this->validate();
        $writer->write([
            [
                'name'  => SchemaNS::CBC . 'LineExtensionAmount',
                'value' => CurrencyOperations::formatAmount($this->LineExtensionAmount, self::DECIMALS),
                'attributes' => ['currencyID' => $this->currencyID]
            ],
            [
                'name'  => SchemaNS::CBC . 'TaxInclusiveAmount',
                'value' => CurrencyOperations::formatAmount($this->TaxInclusiveAmount, self::DECIMALS),
                'attributes' => ['currencyID' => $this->currencyID]
            ],
            [
                'name' => SchemaNS::CBC . 'AllowanceTotalAmount',
                'value' => CurrencyOperations::formatAmount($this->AllowanceTotalAmount, self::DECIMALS),
                'attributes' => ['currencyID' => $this->currencyID]
            ],
            [
                'name' => SchemaNS::CBC . 'PayableAmount',
                'value' => CurrencyOperations::formatAmount($this->PayableAmount, self::DECIMALS),
                'attributes' => ['currencyID' => $this->currencyID]
            ],
        ]);
    }
    public function getCurrencyID() {
        return $this->currencyID;
    }

    public function setCurrencyID($currencyID) {
        $this->currencyID = $currencyID;
        return $this;
    }

    public function getLineExtensionAmount() {
        return $this->LineExtensionAmount;
    }

    public function setLineExtensionAmount($LineExtensionAmount) {
        $this->LineExtensionAmount = $LineExtensionAmount;
        return $this;
    }

    public function getTaxInclusiveAmount() {
        return $this->TaxInclusiveAmount;
    }

    public function setTaxInclusiveAmount($TaxInclusiveAmount) {
        $this->TaxInclusiveAmount = $TaxInclusiveAmount;
        return $this;
    }

    public function getAllowanceTotalAmount() {
        return $this->AllowanceTotalAmount;
    }

    public function setAllowanceTotalAmount($AllowanceTotalAmount) {
        $this->AllowanceTotalAmount = $AllowanceTotalAmount;
        return $this;
    }

    public function getPayableAmount() {
        return $this->PayableAmount;
    }

    public function setPayableAmount($PayableAmount) {
        $this->PayableAmount = $PayableAmount;
        return $this;
    }

    public function getValidations() {
        return $this->validations;
    }

    public function setValidations($validations) {
        $this->validations = $validations;
        return $this;
    }

}
