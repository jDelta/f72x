<?php

/**
 * MÓDULO DE EMISIÓN ELECTRÓNICA F72X
 * UBL 2.1
 * Version 1.0
 * 
 * Copyright 2019, Jaime Cruz
 */

namespace F72X\Sunat\Document;

use F72X\Company;
use F72X\Tools\TemplateMgr;
use F72X\Tools\LogoMgr;
use F72X\Tools\QrGenerator;
use F72X\Sunat\DataMap;
use F72X\Sunat\SunatVars;
use F72X\Sunat\Operations;
use F72X\Sunat\Catalogo;
use F72X\UblComponent\SchemaNS;
use F72X\UblComponent\CreditNote;
use Sabre\Xml\Writer;

class NotaCredito extends CreditNote {

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
        $this->addDocumentItems('CreditNoteLine');
        // Impuestos
        $this->addDocumentTaxes();
        // Totales
        $this->addInvoiceLegalMonetaryTotal();
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
            SchemaNS::CAC . 'LegalMonetaryTotal'      => $this->LegalMonetaryTotal
        ]);

        // Detalle
        foreach ($this->CreditNoteLines as $Line) {
            $writer->write([
                SchemaNS::CAC . 'CreditNoteLine' => $Line
            ]);
        }
    }
    public function getReadyToPrintData() {
        $dataMap = $this->getDataMap();
        $currency = Catalogo::getCurrencyPlural($dataMap->getCurrencyCode());
        $payableAmount = $dataMap->getPayableAmount();
        $payableInWords = Operations::getAmountInWords($payableAmount, $currency);
        return [
            'companyRuc'           => Company::getRUC(),
            'companyAddress'       => Company::getAddress(),
            'companyCity'          => Company::getCity(),
            'edocHeaderContent'    => Company::getEdocHeaderContent(),
            'edocFooterContent'    => Company::getEdocFooterContent(),
            'documentSeries'       => $dataMap->getDocumentSeries(),
            'documentNumber'       => $dataMap->getDocumentNumber(),
            'officialDocumentName' => $dataMap->getOfficialDocumentName(),
            'currency'             => $currency,
            'customerRegName'      => $dataMap->getCustomerRegName(),
            'customerDocNumber'    => $dataMap->getCustomerDocNumber(),
            'customerAddress'      => $dataMap->getCustomerAddress(),
            'issueDate'            => $dataMap->getIssueDate()->format('d-m-Y'),
            'igvPercent'           => SunatVars::IGV_PERCENT,
            'logo'                 => LogoMgr::getLogoString(),
            'qr'                   => QrGenerator::getQrString($dataMap), // QR Code
            'taxableOperations'    => Operations::formatAmount($dataMap->getTotalTaxableOperations()),    // Total operaciones gravadas
            'freeOperations'       => Operations::formatAmount($dataMap->getTotalFreeOperations()),       // Total operaciones gratuitas
            'unaffectedOperations' => Operations::formatAmount($dataMap->getTotalUnaffectedOperations()), // Total operaciones inafectas
            'exemptedOperations'   => Operations::formatAmount($dataMap->getTotalExemptedOperations()),   // Total operaciones exoneradas
            'totalAllowances'      => Operations::formatAmount($dataMap->getTotalAllowances()),           // Total operaciones exoneradas
            'igvAmount'            => Operations::formatAmount($dataMap->getIGV()),                       // Total a pagar
            'payableAmount'        => Operations::formatAmount($payableAmount),                           // Total a pagar
            'payableInWords'       => $payableInWords,                          // Monto en palabras
            'items'                => self::getReadyToPrintDataItems($dataMap),  // Items
            'noteType'                    => $dataMap->getNoteType(),
            'affectedDocumentId'          => $dataMap->getNoteAffectedDocId(),
            'affectedDocumentOficialName' => Catalogo::getOfficialDocumentName($dataMap->getNoteAffectedDocType()),
            'note'                        => $dataMap->getNoteDescription()
        ];
    }

    private static function getReadyToPrintDataItems(DataMap $inv) {
        $Items = $inv->getItems();
        $ln = $Items->getCount();
        $items2 = [];
        for ($i = 0; $i < $ln; $i++) {
            $items2[]= [
                'productCode'       => $Items->getProductCode($i),
                'quantity'          => $Items->getQunatity($i),
                'unitName'          => Catalogo::getUnitName($Items->getUnitCode($i)),
                'unitBillableValue' => Operations::formatAmount($Items->getUnitBillableValue($i)),
                'itemPayableAmount' => Operations::formatAmount($Items->getPayableAmount($i)),
                'description'       => $Items->getDescription($i)
            ];
        }
        return $items2;
    }
}
