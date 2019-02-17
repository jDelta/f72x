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
use F72X\F72X;
use F72X\Company;
use F72X\Repository;
use F72X\Object\DocumentIssuer;
use F72X\Tools\TplRenderer;
use F72X\Tools\XmlDSig;

/**
 * AbstractDocument
 * 
 * This is base for all kind of documents
 */
abstract class AbstractDocument implements DocumentInterface {

    /**
     * The document's signature id.
     * 
     * By now this must be 'SignIMM' as in:
     * {@see \Greenter\XMLSecLibs\XMLSecurityDSig} line 70
     * 
     */
    const SIGNATURE_ID = 'SignIMM';

    /**
     * The Peruvian currency code
     */
    const LOCAL_CURRENCY_CODE = 'PEN';

    /**
     * The document's id.
     * 
     * @var string 
     */
    protected $id;

    /**
     * By default all the documents use series, however it's false in case of
     * Resumenes y Comunicaciones de baja.
     * @var type 
     */
    protected $usesSeries = true;

    /**
     * The document's series.
     * 
     * @var string 
     */
    protected $series;

    /**
     * The document's series prefix
     * 
     * Use:
     *  F: Factura
     *  B: Boleta
     *  P: Percepción
     *  R: Retención
     * 
     * @var string 
     */
    protected $seriesPrefix;

    /**
     * The document's number.
     * 
     * @var string 
     */
    protected $number;

    /**
     * The document's number max length
     * 
     * Default: 8 for Boletas, Facturas, Nota de crédito, Nota de débito,
     * Percepción y Retención.
     * 
     * Use: 5 for Resumen diario y comunicacón de baja.
     * 
     * @var int 
     */
    protected $numberMaxLength = 8;

    /**
     * The date when the document is generated.
     * 
     * @var DateTime 
     */
    protected $issueDate;

    /**
     * The document's filename.
     * 
     * @var string 
     */
    protected $fileName;

    /**
     * The entity that produces the document usually the company.
     * 
     * @var DocumentIssuer 
     */
    protected $issuer;

    /**
     * The document's lines.
     * 
     * @var array 
     */
    protected $lines;

    /**
     * Use this to set the default fields for the document lines.
     * @var array 
     */
    protected $lineDefaults = [];

    /**
     * Use this to  indicate the template that the document is going to use in
     * the XML file creation.
     * 
     * @var string
     */
    protected $xmlTplName;

    /**
     * Saves the received data by the constructor.
     * 
     * @var array 
     */
    private $rawData;

    /**
     * Saves the parsed data that is returned by the *parseInput* method.
     * @var array
     */
    private $parsedData;

    /**
     * Creates the document
     * 
     * @param array $inputData
     */
    public function __construct(array $inputData) {
        $this->processInput($inputData);
        // Set Number
        $this->setNumber();
        // Set Series
        if ($this->usesSeries) {
            $this->setSeries();
        }

        // Set Common fields
        $this->setCommonFields();
        // Create an issuer instance
        $this->issuer = new DocumentIssuer();
        // Sets the company fields such as (RUC, Razón Social, etc.)
        $this->setIssuerFields();
        // Sets the fields related to the tipe of document.
        $this->setBodyFields();
        /**
         * Set Document ID This is last because some
         * fields depend on issuer and body fields.
         */
        $this->setId();
        // Set Filename
        $this->setFileName();
    }

    protected function setNumber() {
        $this->number = str_pad($this->parsedData['documentNumber'], $this->numberMaxLength, '0', STR_PAD_LEFT);
        return $this;
    }

    protected function setSeries() {
        $this->series = $this->seriesPrefix . $this->parsedData['documentSeries'];
        return $this;
    }

    /**
     * Sets the document's id
     * 
     * This can be overridden to create a new id format
     * Default SERIES-NUMBER
     */
    protected function setId() {
        $this->id = $this->series . '-' . $this->number;
    }

    private function setCommonFields() {
        $this->issueDate = $this->parsedData['issueDate'];
    }

    protected function setFileName() {
        // The  file name
        $this->fileName = $this->issuer->getIdDocNumber() . '-' . $this->id;
    }

    private function processInput(array $in) {
        $this->rawData = $in;
        $out1 = $this->parseCommonFields($in);
        $out2 = $this->parseInput($out1);
        $this->parsedData = $out2;
    }

    private function parseCommonFields(array $in) {
        $out = $in;
        $out['issueDate'] = new DateTime($in['issueDate']);
        return $out;
    }

    /**
     * Parses the raw input data
     * @param array $in The input data
     * @return array The parsed data
     */
    abstract protected function parseInput(array $in);

    public function parseInputLines(array $rawLines) {
        $parsedLines = [];
        foreach ($rawLines as $line) {
            $line = array_merge($this->lineDefaults, $line);
            $parsedLines[] = $this->parseInputLine($line);
        }
        return $parsedLines;
    }

    /**
     * 
     * @param array $in The raw input line
     * @return array
     */
    public function parseInputLine(array $in) {
        return $in;
    }

    public function setIssuerFields() {
        $this->issuer->setIdDocNumber(Company::getRUC());
        $this->issuer->setRegName(Company::getCompanyName());
    }

    /**
     * Returns the base fields for an XML file rendering.
     * @return array
     */
    protected function getBaseFieldsForXml() {
        $issuer = $this->issuer;
        return [
            'id' => $this->id,
            'issueDate' => $this->issueDate->format('Y-m-d'),
            'issuer' => [
                'idDocType' => $issuer->getIdDocType(),
                'idDocNumber' => $issuer->getIdDocNumber(),
                'regName' => $issuer->getRegName(),
            ],
            'lines' => $this->getLinesForXml($this->lines)
        ];
    }

    /**
     * Returns the parsed lines for the XML file rendering.
     * 
     * @param array $in The input data
     * @return array
     */
    protected function getLinesForXml(array $in) {
        $out = [];
        foreach ($in as $line) {
            $out[] = $this->getLineForXml($line);
        }
        return $out;
    }

    /**
     * Returns the parsed line for the XML file rendering.
     * 
     * @param array $in The input data
     * @return array
     */
    protected function getLineForXml(array $in) {
        return $in;
    }

    /**
     * Generates the document base files
     * Witch are the next:
     *     Document input
     *     The XML file
     *     The signed XML file
     *     The ZIP file
     */
    public function generateFiles() {
        $this->saveDocumentInput();
        $this->generateXmlFile();
        $this->signXmlFile();
        $this->generateZipFile();
    }

    public function saveDocumentInput() {
        Repository::saveDocumentInput($this->fileName, json_encode($this->getRawData(), JSON_PRETTY_PRINT));
    }

    /**
     * Generates the document's XML file
     */
    public function generateXmlFile() {
        $tplRenderer = TplRenderer::getRenderer(F72X::getSrcDir() . '/templates');
        $xmlData = $this->getDataForXml();
        $xmlData['signatureId'] = self::SIGNATURE_ID;
        $xmlContent = $tplRenderer->render($this->xmlTplName, $xmlData);
        Repository::saveDocument($this->fileName, $xmlContent);
    }

    /**
     * Signs the XML document witch should be generated previously.
     */
    public function signXmlFile() {
        XmlDSig::sign($this->fileName);
    }

    /**
     * Generates the document's ZIP file
     */
    public function generateZipFile() {
        Repository::zipDocument($this->fileName);
    }

    public function getId() {
        return $this->id;
    }

    public function getSeries() {
        return $this->series;
    }

    public function getNumber() {
        return $this->number;
    }

    public function getIssueDate() {
        return $this->issueDate;
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function getIssuer() {
        return $this->issuer;
    }

    public function getLines() {
        return $this->lines;
    }

    public function getLineDefaults() {
        return $this->lineDefaults;
    }

    public function getXmlTplName() {
        return $this->xmlTplName;
    }

    public function getRawData() {
        return $this->rawData;
    }

    public function getParsedData() {
        return $this->parsedData;
    }

    public function setIssueDate(DateTime $issueDate) {
        $this->issueDate = $issueDate;
        return $this;
    }

    public function setIssuer(DocumentIssuer $issuer) {
        $this->issuer = $issuer;
        return $this;
    }

    public function setXmlTplName($xmlTplName) {
        $this->xmlTplName = $xmlTplName;
        return $this;
    }

}
