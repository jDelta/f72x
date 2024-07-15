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

class DespatchDocumentReference extends BaseComponent
{

    protected $ID;
    protected $DocumentTypeCode;

    function xmlSerialize(Writer $writer): void
    {
        $writer->write([
            SchemaNS::CBC . 'ID' => $this->ID,
            [
                'name' => SchemaNS::CBC . 'DocumentTypeCode',
                'value' => $this->DocumentTypeCode,
                'attributes' => [
                    'listAgencyName' => 'PE:SUNAT',
                    'listName' => 'SUNAT:Identificador de guía relacionada',
                    'listURI' => 'urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo12'
                ]
            ]
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
