<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\UblComponent;

use Sabre\Xml\Writer;

class TaxScheme extends BaseComponent {

    protected $ID;
    protected $IDAttributes = [];
    protected $Name;
    protected $TaxTypeCode;

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
        if (!is_null($this->TaxTypeCode)) {
            $writer->write([
                SchemaNS::CBC . 'TaxTypeCode' => $this->TaxTypeCode
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
    public function getIDAttributes() {
        return $this->IDAttributes;
    }

    public function setIDAttributes($IDAtt) {
        $this->IDAttributes = $IDAtt;
        return $this;
    }

    public function setIDAttribute($attribute, $value) {
        $this->IDAttributes[$attribute] = $value;
        return $this;
    }

    public function getName() {
        return $this->Name;
    }

    public function setName($Name) {
        $this->Name = $Name;
        return $this;
    }

    public function getTaxTypeCode() {
        return $this->TaxTypeCode;
    }

    public function setTaxTypeCode($TaxTypeCode) {
        $this->TaxTypeCode = $TaxTypeCode;
        return $this;
    }

}
