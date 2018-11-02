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
use F72X\Tools\TemplateMgr;
use F72X\Sunat\DataMap;
use F72X\UblComponent\SchemaNS;
use F72X\UblComponent\DebitNote;
use Sabre\Xml\Writer;

class NotaDebito extends DebitNote {

    use BillMixin, NoteMixin;

    protected $UBLVersionID = '2.1';
    protected $CustomizationID = '2.0';

    public function __construct(DataMap $DataMap) {
        $this->dataMap = $DataMap;
        $currencyCode = $DataMap->getCurrencyCode();
        // ID
        $this->setID($DataMap->getDocumentId());
        // Fecha de emisión
        $this->setIssueDate($DataMap->getIssueDate());
        // Tipo de moneda
        $this->setDocumentCurrencyCode($currencyCode);
        // Motivo de emisión
        $this->addDiscrepancyResponse();
        // Motivo de emisión
        $this->addBillingReference();
        // Información de la empresa
        $this->addInvoiceAccountingSupplierParty();
        // Información del cliente
        $this->addInvoiceAccountingCustomerParty();
        // Detalle
        $this->addDocumentItems('DebitNoteLine');
        // Impuestos
        $this->addDocumentTaxes();
        // Totales
        $this->addRequestedMonetaryTotal();
    }

    public function xmlSerialize(Writer $writer) {
        $companyRUC = Company::getRUC();
        $companyName = Company::getCompanyName();
        // SchemaNS::EXT . 'UBLExtensions'
        $UBLExtensions = TemplateMgr::getTpl('UBLExtensions.xml');
        $Signature     = TemplateMgr::getTpl('Signature.xml', [
                    'ruc'         => $companyRUC,
                    'companyName' => $companyName
        ]);
        $this->writeLineJump($writer);
        $writer->writeRaw($UBLExtensions);

        $writer->write([
            SchemaNS::CBC . 'UBLVersionID'         => $this->UBLVersionID,
            SchemaNS::CBC . 'CustomizationID'      => $this->CustomizationID,
            SchemaNS::CBC . 'ID'                   => $this->ID,
            SchemaNS::CBC . 'IssueDate'            => $this->IssueDate->format('Y-m-d'),
            SchemaNS::CBC . 'IssueTime'            => $this->IssueDate->format('H:i:s'),
            SchemaNS::CBC . 'DocumentCurrencyCode' => $this->DocumentCurrencyCode,
            SchemaNS::CAC . 'DiscrepancyResponse'  => $this->DiscrepancyResponse,
            SchemaNS::CAC . 'BillingReference'     => $this->BillingReference
        ]);

        // Despatch Document Reference
        if ($this->DespatchDocumentReference) {
            $writer->write([
                SchemaNS::CAC . 'DespatchDocumentReference' => $this->DespatchDocumentReference
            ]);
        }
        // cac:Signature
        $writer->writeRaw($Signature);
        $writer->write([
            SchemaNS::CAC . 'AccountingSupplierParty' => $this->AccountingSupplierParty,
            SchemaNS::CAC . 'AccountingCustomerParty' => $this->AccountingCustomerParty,
            SchemaNS::CAC . 'TaxTotal'                => $this->TaxTotal,
            SchemaNS::CAC . 'RequestedMonetaryTotal'  => $this->RequestedMonetaryTotal
        ]);

        // Detalle
        foreach ($this->DebitNoteLines as $Line) {
            $writer->write([
                SchemaNS::CAC . 'DebitNoteLine' => $Line
            ]);
        }
    }

}
