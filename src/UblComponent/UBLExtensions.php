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

class UBLExtensions extends BaseComponent
{

    /** @var UBLExtension[] */
    protected $UBLExtensions = [];

    public function xmlSerialize(Writer $writer): void
    {
        foreach ($this->UBLExtensions as $ext) {
            $writer->write([
                SchemaNS::EXT . 'UBLExtension' => $ext
            ]);
        }
    }

    public function getUBLExtensions()
    {
        return $this->UBLExtensions;
    }

    public function setUBLExtensions($UBLExtensions)
    {
        $this->UBLExtensions = $UBLExtensions;
        return $this;
    }

    /**
     *
     * @param UBLExtension $UBLExtension
     * @return $this
     */
    public function addUBLExtension($UBLExtension)
    {
        $this->UBLExtensions[] = $UBLExtension;
        return $this;
    }

}
