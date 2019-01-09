<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat\Document;

interface DocumentInterface {

    /**
     * Set the supplier's information such as RUC, name and others
     */
    public function setAccountingSupplierFields();

    /**
     * Sets the Document identification fields
     */
    public function setDocumentIdentificationFields();

    /**
     * Sets the Body fields
     */
    public function setBodyFields();


    /**
     * Parses the raw input lines, is should be implemented using an each function
     * and calling to *parseInputLine for each line in order to reduce complexity
     * @param array $rawLines
     * @return array The parsed lines
     */
    public function parseInputLines(array $rawLines);

    /**
     * Parses the raw input line
     * @param array $rawInputLine
     * @return array The parsed line
     */
    public function parseInputLine(array $rawInputLine);

    /**
     * Returns the an array with data ready to be placed in the document's XML template
     * @param array $input
     * @return array The parsed data
     */
    public function getDataForXml();

    /**
     * Writes the document files
     */
    public function generateFiles();

    /**
     * Writes the document XML file
     */
    public function generateXmlFile();
}
