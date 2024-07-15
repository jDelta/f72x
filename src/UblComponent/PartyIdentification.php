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

class PartyIdentification extends BaseComponent
{

    protected $ID;
    protected $IDAttributes;

    function xmlSerialize(Writer $writer): void
    {
        $writer->write([
            'name' => SchemaNS::CBC . 'ID',
            'value' => $this->ID,
            'attributes' => $this->IDAttributes
        ]);
    }

    public function getID()
    {
        return $this->ID;
    }

    public function setID($ID)
    {
        $this->ID = $ID;
        return $this;
    }

}
