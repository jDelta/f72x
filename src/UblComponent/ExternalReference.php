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

class ExternalReference extends BaseComponent
{

    protected $URI;

    function xmlSerialize(Writer $writer): void
    {
        $writer->write([
            SchemaNS::CBC . 'URI' => $this->URI
        ]);
    }

    public function getURI()
    {
        return $this->URI;
    }

    public function setURI($URI)
    {
        $this->URI = $URI;
        return $this;
    }

}
