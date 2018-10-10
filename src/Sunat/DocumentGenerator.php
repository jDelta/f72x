<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat;

use F72X\Tools\XmlService;
use F72X\Tools\XmlDSig;
use F72X\Tools\FileService;
use F72X\Company;
use F72X\Repository;
use F72X\Sunat\Document\SunatDocument;
use F72X\Sunat\Document\Factura;
use F72X\Sunat\Document\Boleta;
use F72X\Exception\InvalidInputException;

class DocumentGenerator {

    /**
     * Generar Factura
     * 
     * Produce los documentos electronicos listos para ser enviados a SUNAT.
     * 
     * @param array $data
     * @param string $currencyType
     */
    public static function generateFactura(array $data, $currencyType = 'PEN') {
        // Validate input
        self::validateData($data, Catalogo::CAT1_FACTURA);
        // Core invoice
        $Invoice = new InvoiceDocument($data, Catalogo::CAT1_FACTURA, $currencyType);
        // Documento XML para la factura
        $XmlDoc = new Factura($Invoice);
        self::processSutatDoc($XmlDoc);
        return $XmlDoc;
    }

    /**
     * Generar Boleta
     * 
     * Produce los documentos electronicos listos para ser enviados a SUNAT.
     * 
     * @param array $data
     * @param string $currencyType
     */
    public static function generateBoleta(array $data, $currencyType = 'PEN') {
        // Validate input
        self::validateData($data, Catalogo::CAT1_BOLETA);
        // Core invoice
        $Invoice = new InvoiceDocument($data, Catalogo::CAT1_BOLETA, $currencyType);
        // Documento XML para la factura
        $XmlDoc = new Boleta($Invoice);
        self::processSutatDoc($XmlDoc);
        return $XmlDoc;
    }

    private static function validateData(array $data, $type) {
        $validator = new InputValidator($data, $type);
        // Input validation
        if (!$validator->isValid()) {
            throw new InvalidInputException($validator->getErrors());
        }
    }

    private static function processSutatDoc(SunatDocument $XmlDoc) {
        // Save Document
        self::saveInvoice($XmlDoc);
        // Sign Document
        self::singInvoice($XmlDoc);
        // Compress Document
        self::zipInvoice($XmlDoc);
    }

    private static function singInvoice(SunatDocument $Document) {
        $billName = $Document->getBillName();
        XmlDSig::sign($billName);
    }

    private static function zipInvoice(SunatDocument $Document) {
        $billName = $Document->getBillName();
        FileService::doZip("$billName.xml");
    }

    private static function saveInvoice(SunatDocument $invoice) {
        $repository = Company::getRepositoryPath();
        $xmlService = new XmlService('1.0', 'ISO-8859-1');

        $xmlService->namespaceMap = [
            "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"                        => '',
            "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"      => 'cac',
            "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"          => 'cbc',
            "urn:un:unece:uncefact:documentation:2"                                         => 'ccts',
            "http://www.w3.org/2000/09/xmldsig#"                                            => 'ds',
            "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"      => 'ext',
            "urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2"             => 'qdt',
            "urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2"   => 'udt',
            "http://www.w3.org/2001/XMLSchema-instance"                                     => 'xsi'
        ];
        $billName = $invoice->getBillName();
        $billContent = $xmlService->write('Invoice', $invoice);
        Repository::saveBill($billName, $billContent);
    }

}
