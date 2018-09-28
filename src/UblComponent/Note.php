<?php

/**
 * FACTURA ELECTRÓNICA SUNAT
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\UblComponent;

use Sabre\Xml\Writer;

class Note extends BaseComponent {

    /** @var string */
    protected $languageLocaleID;

    /** @var string */
    protected $Value;

    public function __construct($Value) {
        $this->Value = $Value;
    }

    function xmlSerialize(Writer $writer) {
        $writer->write([
            'name'  => SchemaNS::CBC . 'Note',
            'value' => $this->Value,
            'attributes' => [
                'languageLocaleID'  => $this->languageLocaleID
            ]
        ]);
    }

    public function getLanguageLocaleID() {
        return $this->languageLocaleID;
    }

    public function setLanguageLocaleID($languageLocaleID) {
        $this->languageLocaleID = $languageLocaleID;
        return $this;
    }

    public function getValue() {
        return $this->Value;
    }

    public function setValue($Value) {
        $this->Value = $Value;
        return $this;
    }

}
