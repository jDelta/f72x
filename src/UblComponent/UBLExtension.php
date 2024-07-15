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

class UBLExtension extends BaseComponent
{

    /** @var mixed */
    protected $ExtensionContent;

    public function xmlSerialize(Writer $writer): void
    {
        $writer->write([SchemaNS::EXT . 'ExtensionContent' => $this->ExtensionContent]);
    }

    public function getExtensionContent()
    {
        return $this->ExtensionContent;
    }

    public function setExtensionContent($ExtensionContent)
    {
        $this->ExtensionContent = $ExtensionContent;
        return $this;
    }

}
