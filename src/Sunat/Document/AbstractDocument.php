<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat\Document;

use F72X\F72X;
use F72X\Company;
use F72X\Repository;
use F72X\Tools\TplRenderer;

abstract class AbstractDocument implements DocumentInterface {

    protected $rawData;
    protected $parsedData;
    protected $accountingSupplierDocType = 6; // Type: RUC
    protected $accountingSupplierDocNumber;
    protected $accountingSupplierRegName;
    protected $documentSeries;
    protected $documentNumber;
    protected $documentId;
    protected $documentFileName;
    protected $documentLines;
    protected $xmlTplName;

    /**
     * Use this to set the default fields for the document lines
     * @var array 
     */
    protected $documentLineDefaults = [];

    public function __construct(array $inputData) {
        $this->processInput($inputData);
        $this->setBodyFields();
        // Datos de contribuyente: RUC, Razón Social, etc.
        $this->setAccountingSupplierFields();
        // Docuemnt: ID, Series, Number and filename
        $this->setDocumentIdentificationFields();
    }

    private function processInput(array $inputData) {
        $parsedData = $this->parseInput($inputData);
        $this->rawData = $inputData;
        $this->parsedData = $parsedData;
    }
    /**
     * Parses the raw input data
     * @param array $rawInput
     * @return array The parsed data
     */
    abstract protected function parseInput(array $rawInput);


    public function parseInputLines(array $rawLines) {
        $parsedLines = [];
        foreach ($rawLines as $line) {
            $line = array_merge($this->documentLineDefaults, $line);
            $parsedLines[] = $this->parseInputLine($line);
        }
        return $parsedLines;
    }

    public function parseInputLine(array $rawInputLine) {
        return $rawInputLine;
    }

    public function setAccountingSupplierFields() {
        $this->accountingSupplierDocNumber = Company::getRUC();
        $this->accountingSupplierRegName = Company::getCompanyName();
    }

    public function generateXmlFile() {
        $tplRenderer = TplRenderer::getRenderer(F72X::getSrcDir() . '/templates');
        $xmlContent = $tplRenderer->render($this->xmlTplName, $this->getDataForXml());
        Repository::saveDocument($this->documentFileName, $xmlContent);
    }

    public function getRawData() {
        return $this->rawData;
    }

    public function getParsedData() {
        return $this->parsedData;
    }

    public function getAccountingSupplierDocType() {
        return $this->accountingSupplierDocType;
    }

    public function getAccountingSupplierDocNumber() {
        return $this->accountingSupplierDocNumber;
    }

    public function getAccountingSupplierRegName() {
        return $this->accountingSupplierRegName;
    }

    public function getDocumentSeries() {
        return $this->documentSeries;
    }

    public function getDocumentNumber() {
        return $this->documentNumber;
    }

    public function getDocumentId() {
        return $this->documentId;
    }

    public function getDocumentFileName() {
        return $this->documentFileName;
    }

    public function getDocumentLines() {
        return $this->documentLines;
    }

    public function setRawData($rawData) {
        $this->rawData = $rawData;
        return $this;
    }

    public function setParsedData($parsedData) {
        $this->parsedData = $parsedData;
        return $this;
    }

    public function setAccountingSupplierDocType($docType) {
        $this->accountingSupplierDocType = $docType;
        return $this;
    }

    public function setAccountingSupplierDocNumber($docNumber) {
        $this->accountingSupplierDocNumber = $docNumber;
        return $this;
    }

    public function setAccountingSupplierRegName($registrationName) {
        $this->accountingSupplierRegName = $registrationName;
        return $this;
    }

    public function setDocumentSeries($series) {
        $this->documentSeries = $series;
        return $this;
    }

    public function setDocumentNumber($number) {
        $this->documentNumber = $number;
        return $this;
    }

    public function setDocumentId($id) {
        $this->documentId = $id;
        return $this;
    }

    public function setDocumentFileName($documentFileName) {
        $this->documentFileName = $documentFileName;
        return $this;
    }

    public function setDocumentLines($documentLines) {
        $this->documentLines = $documentLines;
        return $this;
    }

}
