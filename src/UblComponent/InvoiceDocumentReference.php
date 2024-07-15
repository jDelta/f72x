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

class InvoiceDocumentReference extends BaseComponent
{

    protected $ID;
    protected $DocumentTypeCode;

    function xmlSerialize(Writer $writer): void
    {
        $writer->write([
            SchemaNS::CBC . 'ID' => $this->ID,
            SchemaNS::CBC . 'DocumentTypeCode' => $this->DocumentTypeCode
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

    public function getDocumentTypeCode()
    {
        return $this->DocumentTypeCode;
    }

    public function setDocumentTypeCode($DocumentTypeCode)
    {
        $this->DocumentTypeCode = $DocumentTypeCode;
        return $this;
    }

}
