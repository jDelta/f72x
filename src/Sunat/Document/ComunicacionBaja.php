<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat\Document;

use DateTime;

class ComunicacionBaja extends AbstractSummary {

    protected $summaryIdPrefix = 'RA';
    protected $xmlTplName = 'VoidedDocuments.xml';

    protected function parseInput(array $input) {
        $parsedData = $input;
        $parsedData['issueDate']     = new DateTime($input['issueDate']);
        $parsedData['referenceDate'] = new DateTime($input['referenceDate']);
        $parsedData['items']         = $this->parseInputLines($input['items']);
        return $parsedData;
    }

    public function setBodyFields() {
        $data = $this->parsedData;
        $this->issueDate     = $data['issueDate'];
        $this->referenceDate = $data['referenceDate'];
        // Lines
        $this->documentLines = $data['items'];
    }

    public function getDataForXml() {
        return [
            'documentId'                  => $this->documentId,
            'issueDate'                   => $this->issueDate->format('Y-m-d'),
            'referenceDate'               => $this->referenceDate->format('Y-m-d'),
            'accountingSupplierDocType'   => $this->accountingSupplierDocType,
            'accountingSupplierDocNumber' => $this->accountingSupplierDocNumber,
            'accountingSupplierRegName'   => $this->accountingSupplierRegName,
            'lines'                       => $this->documentLines
        ];
    }
    public function generateFiles() {
        $this->generateXmlFile();
    }
    public function getIssueDate() {
        return $this->issueDate;
    }

    public function getReferenceDate() {
        return $this->referenceDate;
    }

    public function setIssueDate(DateTime $issueDate) {
        $this->issueDate = $issueDate;
        return $this;
    }

    public function setReferenceDate(DateTime $referenceDate) {
        $this->referenceDate = $referenceDate;
        return $this;
    }

}
