<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat\Document;

use DateTime;

/**
 * AbstractSummary
 * 
 * Base class for Resumen Diario y Comunicación de baja.
 */
abstract class AbstractSummary extends AbstractDocument {

    protected $idPrefix; // RA|RC
    protected $usesSeries = false;
    protected $numberMaxLength = 5;
    /**
     * @var DateTime 
     */
    protected $referenceDate;

    protected function parseInput(array $rawInput) {
        $parsedData = $rawInput;
        $parsedData['referenceDate'] = new DateTime($rawInput['referenceDate']);
        $parsedData['items'] = $this->parseInputLines($rawInput['items']);
        return $parsedData;
    }

    public function setBodyFields() {
        $data = $this->getParsedData();
        $this->referenceDate = $data['referenceDate'];
        // Lines
        $this->lines = $data['items'];
    }

    /**
     * Sets: Document: Number, ID and filename
     */
    protected function setId() {
        $this->id = $this->idPrefix . '-' . $this->issueDate->format('Ymd') . '-' . $this->number;
    }

    public function getDataForXml() {
        // Get base fields
        $data = $this->getBaseFieldsForXml();
        $data['referenceDate'] = $this->referenceDate->format('Y-m-d');
        return $data;
    }

    /**
     * Returns the prefix that is added at the beginning of the document ID
     * @return string RA|RC
     */
    public function getIdPrefix() {
        return $this->idPrefix;
    }

    public function getReferenceDate() {
        return $this->referenceDate;
    }

    public function setReferenceDate(DateTime $referenceDate) {
        $this->referenceDate = $referenceDate;
        return $this;
    }

}
