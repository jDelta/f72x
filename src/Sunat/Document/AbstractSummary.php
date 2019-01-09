<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat\Document;

abstract class AbstractSummary extends AbstractDocument {

    protected $summaryIdPrefix;

    /**
     * @var DateTime 
     */
    protected $issueDate;

    /**
     * @var DateTime 
     */
    protected $referenceDate;

    /**
     * Sets: Document: Number, ID and filename
     */
    public function setDocumentIdentificationFields() {
        $parsedData = $this->parsedData;
        // Max:  5 digits
        $this->documentNumber = str_pad($parsedData['documentNumber'], 5, '0', STR_PAD_LEFT);
        // <PREFIX>-<Fecha de generación del archivo YYYYMMDD>-<Número Correlativo>
        $this->documentId = $this->summaryIdPrefix . '-' . $this->issueDate->format('Ymd') . '-' . $this->documentNumber;
        // The  file name
        $this->documentFileName = $this->accountingSupplierDocNumber . '-' . $this->documentId;
    }

}
