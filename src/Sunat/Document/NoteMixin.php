<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat\Document;

use F72X\Company;
use F72X\Sunat\DataMap;
use F72X\Sunat\InvoiceItems;
use F72X\Sunat\Catalogo;
use F72X\Sunat\SunatVars;
use F72X\Tools\UblHelper;
use F72X\UblComponent\OrderReference;
use F72X\UblComponent\Party;
use F72X\UblComponent\PartyIdentification;
use F72X\UblComponent\PartyName;
use F72X\UblComponent\AccountingSupplierParty;
use F72X\UblComponent\AccountingCustomerParty;
use F72X\UblComponent\PartyLegalEntity;
use F72X\UblComponent\TaxTotal;
use F72X\UblComponent\TaxSubTotal;
use F72X\UblComponent\TaxCategory;
use F72X\UblComponent\TaxScheme;
use F72X\UblComponent\LegalMonetaryTotal;
use F72X\UblComponent\InvoiceLine;
use F72X\UblComponent\CreditNoteLine;
use F72X\UblComponent\DebitNoteLine;
use F72X\UblComponent\PricingReference;
use F72X\UblComponent\AlternativeConditionPrice;
use F72X\UblComponent\Item;
use F72X\UblComponent\DiscrepancyResponse;
use F72X\UblComponent\BillingReference;
use F72X\UblComponent\InvoiceDocumentReference;

trait NoteMixin {

    public function addDiscrepancyResponse() {

        // Data
        $dataMap = $this->dataMap;
        $affectedDocId = $dataMap->getNoteAffectedDocId();
        $type = $dataMap->getNoteType();
        $description = $dataMap->getNoteDescription();

        // XML Nodes
        $DiscrepancyResponse = new DiscrepancyResponse();
        $DiscrepancyResponse
                ->setReferenceID($affectedDocId)
                ->setResponseCode($type)
                ->setDescription($description);

        // Add Node
        $this->setDiscrepancyResponse($DiscrepancyResponse);
    }

    public function addBillingReference() {

        // Data
        $dataMap = $this->dataMap;
        $affDocId = $dataMap->getNoteAffectedDocId();
        $affDocType = $dataMap->getNoteAffectedDocType();

        // XML Nodes
        $BillingReference = new BillingReference();
        $InvoiceDocumentReference = new InvoiceDocumentReference();
        $BillingReference
                ->setInvoiceDocumentReference($InvoiceDocumentReference
                        ->setID($affDocId)
                        ->setDocumentTypeCode($affDocType));

        // Add Node
        $this->setBillingReference($BillingReference);
    }

}
