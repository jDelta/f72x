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
use F72X\UblComponent\Invoice;
use F72X\Sunat\DataMap;
use F72X\Sunat\SunatVars;
use F72X\Sunat\Operations;
use F72X\Sunat\Catalogo;
use F72X\Tools\UblHelper;
use F72X\Tools\LogoMgr;
use F72X\Tools\QrGenerator;

abstract class SunatInvoice extends Invoice {

    use BillMixin;
    const UBL_VERSION_ID = '2.1';
    const CUSTUMIZATION_ID = '2.0';

    public function __construct(DataMap $DataMap) {
        $this->dataMap = $DataMap;
        $currencyCode = $DataMap->getCurrencyCode();
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
        // Información de pago
        $this->addPaymentTerms();
        // Total items
        $this->setLineCountNumeric($DataMap->getTotalItems());
        // Detalle
        $this->addDocumentItems('InvoiceLine');
        // Impuestos
        $this->addDocumentTaxes();
        // Descuentos globales
        $ac = $DataMap->getAllowancesAndCharges();
        $baseAmount = $DataMap->getBillableAmount();
        UblHelper::addAllowancesCharges($this, $ac, $baseAmount, $currencyCode);
        // Totales
        $this->addInvoiceLegalMonetaryTotal();
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
            'items'                => self::getReadyToPrintDataItems($dataMap)      // Items
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
