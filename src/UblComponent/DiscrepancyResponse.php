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
use Sabre\Xml\Element\Cdata;

class DiscrepancyResponse extends BaseComponent {

    protected $ReferenceID;
    protected $ResponseCode;
    protected $Description;

    function xmlSerialize(Writer $writer) {
        $writer->write([
            SchemaNS::CBC . 'ReferenceID'  => $this->ReferenceID,
            SchemaNS::CBC . 'ResponseCode' => $this->ResponseCode,
            SchemaNS::CBC . 'Description'  => new Cdata($this->Description)
        ]);
    }

    public function getReferenceID() {
        return $this->ReferenceID;
    }

    public function setReferenceID($ReferenceID) {
        $this->ReferenceID = $ReferenceID;
        return $this;
    }

    public function getResponseCode() {
        return $this->ResponseCode;
    }

    public function setResponseCode($ResponseCode) {
        $this->ResponseCode = $ResponseCode;
        return $this;
    }

    public function getDescription() {
        return $this->Description;
    }

    public function setDescription($Description) {
        $this->Description = $Description;
        return $this;
    }

}
