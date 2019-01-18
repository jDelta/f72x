<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\UblComponent;

use F72X\Sunat\Operations;
use Sabre\Xml\Writer;

class TaxCategory extends BaseComponent {

    const DECIMALS = 2;

    protected $ID;
    protected $IDAttributes;
    protected $Name;
    protected $Percent;
    protected $TaxExemptionReasonCode;
    protected $TaxExemptionReasonCodeAttributes = [];

    /** @var TaxScheme */
    protected $TaxScheme;

    function xmlSerialize(Writer $writer) {
        $writer->write([
            'name'          => SchemaNS::CBC . 'ID',
            'value'         => $this->ID,
            'attributes'    => $this->IDAttributes
        ]);

        if (!is_null($this->Name)) {
            $writer->write([
                SchemaNS::CBC . 'Name' => $this->Name
            ]);
        }

        if (!is_null($this->Percent)) {
            $writer->write([
                SchemaNS::CBC . 'Percent' => Operations::formatAmount($this->Percent, self::DECIMALS),
            ]);
        }

        if (!is_null($this->TaxExemptionReasonCode)) {
            $writer->write([
                'name'          => SchemaNS::CBC . 'TaxExemptionReasonCode',
                'value'         => $this->TaxExemptionReasonCode,
                'attributes'    => $this->TaxExemptionReasonCodeAttributes
            ]);
        }

        if (!is_null($this->TaxScheme)) {
            $writer->write([
                SchemaNS::CAC . 'TaxScheme' => $this->TaxScheme
            ]);
        }
    }

    public function getID() {
        return $this->ID;
    }

    public function setID($ID) {
        $this->ID = $ID;
        return $this;
    }

    public function getName() {
        return $this->Name;
    }

    public function setName($Name) {
        $this->Name = $Name;
        return $this;
    }

    public function getPercent() {
        return $this->Percent;
    }

    public function setPercent($Percent) {
        $this->Percent = $Percent;
        return $this;
    }

    public function getTaxExemptionReasonCode() {
        return $this->TaxExemptionReasonCode;
    }

    public function setTaxExemptionReasonCode($TaxExemptionReasonCode) {
        $this->TaxExemptionReasonCode = $TaxExemptionReasonCode;
        return $this;
    }

    public function getTaxScheme() {
        return $this->TaxScheme;
    }

    public function setTaxScheme(TaxScheme $TaxScheme) {
        $this->TaxScheme = $TaxScheme;
        return $this;
    }

}
