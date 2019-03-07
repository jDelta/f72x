<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat;

use InvalidArgumentException;
use F72X\Tools\XmlDSig;
use F72X\Tools\PdfGenerator;
use F72X\Repository;
use F72X\Sunat\Catalogo;
use F72X\Tools\XmlService;
use F72X\Sunat\Document\Factura;
use F72X\Sunat\Document\Boleta;
use F72X\Sunat\Document\NotaCredito;
use F72X\Sunat\Document\NotaDebito;
use F72X\Sunat\Document\ResumenDiario;
use F72X\Sunat\Document\ComunicacionBaja;
use F72X\Sunat\Document\Percepcion;
use F72X\Sunat\Document\Retencion;
use F72X\Exception\InvalidInputException;

class DocumentGenerator {

    /**
     * Crear documento electrónico
     * 
     * Crea Factura, Boleta, Nota de crédito y Nota de débito.
     * 
     * Procesa la data proporcionada para el tipo de documento indicado
     * 
     * @param string $shortCode FAC|BOL|NCR|NDE
     * @param array $data
     * @param string $currencyCode
     */
    public static function createDocument($shortCode, array $data) {
        // Validate type
        if (!in_array($shortCode, ['FAC', 'BOL', 'NCR', 'NDE'])) {
            throw new InvalidArgumentException("F72X: El tipo '$shortCode', es invalido use FAC|BOL|NCR|NDE");
        }
        // Set Document Type Code
        $docType = Catalogo::getDocumentType($shortCode);
        $data['documentType'] = $docType;
        // Validate input
        self::validateData($data, $shortCode);
        // Data map
        $dataMap = new DataMap($data, $docType);
        // Generate XML
        if ($docType == Catalogo::DOCTYPE_FACTURA) {
            return new Factura($dataMap);
        }
        if ($docType == Catalogo::DOCTYPE_BOLETA) {
            return new Boleta($dataMap);
        }
        if ($docType == Catalogo::DOCTYPE_NOTA_CREDITO) {
            return new NotaCredito($dataMap);
        }
        return new NotaDebito($dataMap);
    }

    /**
     * 
     * @param string $documentType 01|03|07|08
     * @param string $affectedDocumentType 01|03
     * @param string $baseSeries ###|C##|D##
     * @return string F###|B###|FC##|FD#|#BC##|BD##
     */
    public static function buildDocumentSeries($documentType, $affectedDocumentType, $baseSeries) {
        return Catalogo::getDocumentSeriesPrefix($documentType, $affectedDocumentType) . $baseSeries;
    }

    private static function validateData(array $data, $type) {
        $validator = new InputValidator($data, $type);
        // Input validation
        if (!$validator->isValid()) {
            $validator->throwExc();
        }
    }

    /**
     * 
     * @param Factura|Boleta|NotaCredito|NotaDebito $XmlDoc
     */
    public static function generateFiles($XmlDoc) {
        // Save Input
        self::saveDocumentInput($XmlDoc);
        // Save Document
        self::saveDocument($XmlDoc);
        // Sign Document
        self::signDocument($XmlDoc);
        // Compress Document
        self::zipDocument($XmlDoc);
        // Generate PDF
        self::generatePdf($XmlDoc);
    }

    /**
     * 
     * @param array $data
     * @return ResumenDiario
     */
    public static function createResumenDiario($data) {
        return new ResumenDiario($data);
    }

    /**
     * 
     * @param array $data La data
     * @return ComunicacionBaja
     */
    public static function createComunicacionBaja($data) {
        return new ComunicacionBaja($data);
    }

    /**
     * 
     * @param array $data La data
     * @return Perception
     */
    public static function createPercepcion($data) {
        self::validateData($data, 'PER');
        return new Percepcion($data);
    }

    /**
     * 
     * @param array $data La data
     * @return Perception
     */
    public static function createRetencion($data) {
        return new Retencion($data);
    }

    public static function generateResumenFiles(ResumenDiario $eDocument) {
        $eDocument->generateFiles();
    }

    private static function saveDocumentInput($XmlDoc) {
        $documentName = $XmlDoc->getDocumentName();
        Repository::saveDocumentInput($documentName, json_encode($XmlDoc->getDataMap()->getRawData(), JSON_PRETTY_PRINT));
    }

    private static function signDocument($XmlDoc) {
        $documentName = $XmlDoc->getDocumentName();
        XmlDSig::sign($documentName);
    }

    private static function zipDocument($XmlDoc) {
        $documentName = $XmlDoc->getDocumentName();
        Repository::zipDocument($documentName);
    }

    public static function generatePdf($XmlDoc) {
        $documentName = $XmlDoc->getDocumentName();
        $Invoice = $XmlDoc->getDataMap();
        PdfGenerator::generatePdf($Invoice, $documentName);
    }

    private static function saveDocument($Bill) {
        $xmlService = new XmlService('1.0', 'ISO-8859-1');
        $documentType = $Bill->getDataMap()->getDocumentType();
        // Set namespaces
        $xmlService->namespaceMap = self::getNamespaceMap($documentType);
        $documentName = $Bill->getDocumentName();
        // Xml Root
        $xmlRoot = self::getXmlRoot($documentType);
        $billContent = $xmlService->write($xmlRoot, $Bill);
        Repository::saveDocument($documentName, $billContent);
    }

    /**
     * 
     * @param string $documentType 01|03|07|08
     * @return string Invoice|CreditNote|DebitNote
     */
    private static function getXmlRoot($documentType) {
        switch ($documentType) {
            case Catalogo::DOCTYPE_FACTURA :
            case Catalogo::DOCTYPE_BOLETA : return 'Invoice';
            case Catalogo::DOCTYPE_NOTA_CREDITO : return 'CreditNote';
            case Catalogo::DOCTYPE_NOTA_DEBITO : return 'DebitNote';
        }
    }

    /**
     * 
     * @param string $documentType 01|03|07|08
     * @return array
     */
    private static function getNamespaceMap($documentType) {
        switch ($documentType) {
            case Catalogo::DOCTYPE_FACTURA :
            case Catalogo::DOCTYPE_BOLETA :
                $topNamespace = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';
                break;
            case Catalogo::DOCTYPE_NOTA_CREDITO :
                $topNamespace = 'urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2';
                break;
            case Catalogo::DOCTYPE_NOTA_DEBITO :
                $topNamespace = 'urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2';
                break;
        }
        return [
            $topNamespace => '',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2' => 'cac',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2' => 'cbc',
            'urn:un:unece:uncefact:documentation:2' => 'ccts',
            'http://www.w3.org/2000/09/xmldsig#' => 'ds',
            'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2' => 'ext',
            'urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2' => 'qdt',
            'urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2' => 'udt',
            'http://www.w3.org/2001/XMLSchema-instance' => 'xsi'
        ];
    }

}
