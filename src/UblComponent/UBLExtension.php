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

class UBLExtension extends BaseComponent {

    /** @var mixed */
    protected $ExtensionContent;

    function xmlSerialize(Writer $writer) {
        $writer->write([SchemaNS::EXT . 'ExtensionContent' => $this->ExtensionContent]);
    }

    function getExtensionContent() {
        return $this->ExtensionContent;
    }

    function setExtensionContent($ExtensionContent) {
        $this->ExtensionContent = $ExtensionContent;
        return $this;
    }

}
