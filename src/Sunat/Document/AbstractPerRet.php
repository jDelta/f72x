<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat\Document;

/**
 * AbstractSummary
 * 
 * Base class for Resumen Diario y Comunicación de baja.
 */
abstract class AbstractPerRet extends AbstractDocument {

    protected function parseInput(array $rawInput) {
        $parsedData = $rawInput;
        $parsedData['lines'] = $this->parseInputLines($rawInput['lines']);
        return $parsedData;
    }

    public function setBodyFields() {
        $data = $this->getParsedData();
        // Lines
        $this->lines = $data['lines'];
    }

    public function getDataForXml() {
        $pd = $this->getParsedData();
        // Get base fields
        $data = $this->getBaseFieldsForXml();
        $data['customer'] = $pd['customer'];
        return $data;
    }

}
