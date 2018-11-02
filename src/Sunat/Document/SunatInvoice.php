<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.1
 * 
 * Copyright 2018, Jaime Cruz
 */

namespace F72X\Sunat\Document;

use F72X\UblComponent\Invoice;
use F72X\Sunat\DataMap;
use F72X\Tools\UblHelper;

abstract class SunatInvoice extends Invoice {

    use BillMixin;
    const UBL_VERSION_ID = '2.1';
    const CUSTUMIZATION_ID = '2.0';

    public function __construct(DataMap $DataMap) {
        $this->dataMap = $DataMap;
        $currencyCode = $DataMap->getCurrencyCode();
        $Items = $DataMap->getItems();
        // Invoice Type
        $this->setInvoiceTypeCode($DataMap->getDocumentType());
        // ID
        $this->setID($DataMap->getDocumentId());
        // Tipo de operación
        $this->setProfileID($DataMap->getOperationType());
        // Fecha de emisión
        $this->setIssueDate($DataMap->getIssueDate());
        // Tipo de moneda
        $this->setDocumentCurrencyCode($currencyCode);
        // Orden de compra
        $this->addInvoiceOrderReference();
        // Información de la empresa
        $this->addInvoiceAccountingSupplierParty();
        // Información del cliente
        $this->addInvoiceAccountingCustomerParty();
        // Total items
        $this->setLineCountNumeric($DataMap->getTotalItems());
        // Detalle
        $this->addDocumentItems('InvoiceLine');
        // Impuestos
        $this->addDocumentTaxes();
        // Descuentos globales
        $ac = $DataMap->getAllowancesAndCharges();
        $baseAmount = $Items->getTotalTaxableAmount();
        UblHelper::addAllowancesCharges($this, $ac, $baseAmount, $currencyCode);
        // Totales
        $this->addInvoiceLegalMonetaryTotal();
    }

}
