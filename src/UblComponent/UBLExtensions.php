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

class UBLExtensions extends BaseComponent {

    /** @var UBLExtension[] */
    protected $UBLExtensions = [];

    function xmlSerialize(Writer $writer) {
        foreach ($this->UBLExtensions as $ext) {
            $writer->write([
                SchemaNS::EXT . 'UBLExtension' => $ext
            ]);
        }
    }

    function getUBLExtensions() {
        return $this->UBLExtensions;
    }

    function setUBLExtensions($UBLExtensions) {
        $this->UBLExtensions = $UBLExtensions;
        return $this;
    }

    /**
     * 
     * @param UBLExtension $UBLExtension
     * @return $this
     */
    public function addUBLExtension($UBLExtension) {
        $this->UBLExtensions[] = $UBLExtension;
        return $this;
    }

}
