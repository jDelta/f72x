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

class AccountingSupplierParty extends BaseComponent {

    /** @var Party */
    protected $Party;

    function xmlSerialize(Writer $writer) {
        $writer->write([
            SchemaNS::CAC . 'Party' => $this->Party
        ]);
    }

    public function getParty() {
        return $this->Party;
    }

    public function setParty($Party) {
        $this->Party = $Party;
        return $this;
    }

}
