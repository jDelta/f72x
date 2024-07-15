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

class CommodityClassification extends BaseComponent
{

    protected $ItemClassificationCode;
    protected $validations = ['ItemClassificationCode'];

    function xmlSerialize(Writer $writer): void
    {
        $this->validate();
        $writer->write([
            'name' => SchemaNS::CBC . 'ItemClassificationCode',
            'value' => $this->ItemClassificationCode,
            'attributes' => [
                'listID' => 'UNSPSC',
                'listAgencyName' => 'GS1 US',
                'listName' => 'Item Classification'
            ]
        ]);
    }

    public function getItemClassificationCode()
    {
        return $this->ItemClassificationCode;
    }

    public function setItemClassificationCode($ItemClassificationCode)
    {
        $this->ItemClassificationCode = $ItemClassificationCode;
        return $this;
    }

}
