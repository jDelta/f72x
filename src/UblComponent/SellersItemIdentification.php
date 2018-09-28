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

class SellersItemIdentification extends BaseComponent {

    protected $ID;
    protected $validations = ['ID'];

    function xmlSerialize(Writer $writer) {
        $this->validate();
        $writer->write([
            SchemaNS::CBC . 'ID' => $this->ID
        ]);
    }

    public function getID() {
        return $this->ID;
    }

    public function setID($ID) {
        $this->ID = $ID;
        return $this;
    }

}
