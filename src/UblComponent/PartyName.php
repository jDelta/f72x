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
use Sabre\Xml\Element\Cdata;

class PartyName extends BaseComponent
{

    protected $Name;

    function xmlSerialize(Writer $writer): void
    {
        $writer->write([
            SchemaNS::CBC . 'Name' => new Cdata($this->Name)
        ]);
    }

    public function getName()
    {
        return $this->Name;
    }

    public function setName($Name)
    {
        $this->Name = $Name;
        return $this;
    }

}
