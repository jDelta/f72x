<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 *
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Tools;

use Sabre\Xml\Service;

class XmlService extends Service
{

    private $xmlVersion;
    private $xmlEncoding;
    private $xmlStandalone;
    private $xmlIndentString = '    ';

    public function __construct($xmlVersion = '1.0', $encoding = null, $standalone = null)
    {
        $this->xmlVersion = $xmlVersion;
        $this->xmlEncoding = $encoding;
        $this->xmlStandalone = $standalone;
    }

    public function write($rootElementName, $value, $contextUri = null): string
    {
        $me = $this;
        $w = $this->getWriter();
        $w->openMemory();
        $w->contextUri = $contextUri;
        $w->setIndent(true);
        $w->setIndentString($me->xmlIndentString);
        $w->startDocument($me->xmlVersion, $me->xmlEncoding, $me->xmlStandalone);
        $w->writeElement($rootElementName, $value);
        return $w->outputMemory();
    }

}
