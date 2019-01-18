<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\UblComponent;

use Sabre\Xml\Writer;

class OrderReference extends BaseComponent {

    protected $ID;

    function xmlSerialize(Writer $writer) {
        $me = $this;
        $writer->write([
            SchemaNS::CBC . 'ID' => $me->ID
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
