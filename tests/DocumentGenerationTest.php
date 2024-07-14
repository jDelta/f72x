<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use F72X\Repository;
use F72X\Sunat\Operations;
use F72X\Sunat\Catalogo;
use F72X\Sunat\DocumentGenerator;
use F72X\Sunat\DataMap;
use F72X\Sunat\InvoiceItems;

final class DocumentGenerationTest extends TestCase
{

    public function testRemoveBillDocs()
    {
        $this->expectNotToPerformAssertions();
        Repository::removeFiles('20100454523-03-B001-00003652',  false);
        Repository::removeFiles('20100454523-07-FC01-00000211',  false);
        Repository::removeFiles('20100454523-08-FD01-00000211',  false);
        Repository::removeFiles('20100454523-RC-20171118-00001', false);
        Repository::removeFiles('20100454523-RA-20110402-00001', false);
        Repository::removeFiles('20100454523-P001-00000123',     false);
        Repository::removeFiles('20100454523-R001-00000123',     false);
    }

    public function testGeneratBoleta()
    {
        $this->expectNotToPerformAssertions();
        Repository::removeFiles('20100454523-03-B001-00003652',  false);
        $data = self::getCaseData('boleta');
        $xmlInvice = DocumentGenerator::createDocument('BOL', $data);
        DocumentGenerator::generateFiles($xmlInvice);
    }

    public function testGenerateCreditNote()
    {
        $this->expectNotToPerformAssertions();
        Repository::removeFiles('20100454523-07-FC01-00000211',  false);
        $data = self::getCaseData('notacredito');
        $xmlDoc = DocumentGenerator::createDocument('NCR', $data);
        DocumentGenerator::generateFiles($xmlDoc);
    }

    public function testGenerateDebitNote()
    {
        $this->expectNotToPerformAssertions();
        Repository::removeFiles('20100454523-08-FD01-00000211',  false);
        $data = self::getCaseData('notadebito');
        $xmlDoc = DocumentGenerator::createDocument('NDE', $data);
        DocumentGenerator::generateFiles($xmlDoc);
    }

    public function testResumenDiario()
    {
        $this->expectNotToPerformAssertions();
        Repository::removeFiles('20100454523-RC-20171118-00001', false);
        $data = self::getCaseData('resumen-diario');
        $eDocument = DocumentGenerator::createResumenDiario($data);
        $eDocument->generateFiles();
    }

    public function testComunicacionDeBaja()
    {
        $this->expectNotToPerformAssertions();
        Repository::removeFiles('20100454523-RA-20110402-00001', false);
        $data = self::getCaseData('comunicacion-baja');
        $eDocument = DocumentGenerator::createComunicacionBaja($data);
        $eDocument->generateFiles();
    }

    //    public function testPercepcion() {
    //        Repository::removeFiles('20100454523-P001-00000123',     false);
    //        $data = self::getCaseData('percepcion');
    //        $eDocument = DocumentGenerator::createPercepcion($data);
    //        $eDocument->generateFiles();
    //    }
    //
    //    public function testRetencion() {
    //        Repository::removeFiles('20100454523-R001-00000123',     false);
    //        $data = self::getCaseData('retencion');
    //        $eDocument = DocumentGenerator::createRetencion($data);
    //        $eDocument->generateFiles();
    //    }

    public function xtestDataMapRightCalcsForFactura()
    {
        $in = self::getCaseData('facturas/factura-pago-contado');
        $Invoice = new DataMap($in, Catalogo::DOCTYPE_FACTURA);
        $out = [
            'currencyCode'      => $Invoice->getCurrencyCode(),
            'operationType'     => $Invoice->getOperationType(),
            'documentSeries'    => $Invoice->getDocumentSeries(),
            'documentNumber'    => (int)$Invoice->getDocumentNumber(),
            'customerDocType'   => $Invoice->getCustomerDocType(),
            'customerDocNumber' => $Invoice->getCustomerDocNumber(),
            'customerRegName'   => $Invoice->getCustomerRegName(),
            'customerAddress'   => $Invoice->getCustomerAddress(),
            'issueDate'         => $Invoice->getIssueDate()->format('Y-m-d\TH:i:s'),
            'dueDate'           => $Invoice->getDueDate()->format('Y-m-d\TH:i:s'),
            'purchaseOrder'     => $Invoice->getPurchaseOrder(),
            'allowancesCharges' => $Invoice->getAllowancesAndCharges(),
            'items'             => $Invoice->getRawItems(),
            'totalTaxes'        => (float)Operations::formatAmount($Invoice->getTotalTaxes()),
            'taxableAmount'     => (float)Operations::formatAmount($Invoice->getTaxableAmount()),
            'totalAllowances'   => (float)Operations::formatAmount($Invoice->getTotalAllowances()),
            'payableAmount'     => (float)Operations::formatAmount($Invoice->getPayableAmount())
        ];
        unset($in['documentSeries']);
        unset($out['documentSeries']);
        self::assertEquals($in, $out);
    }

    public function testDataMapRightCalcsForBoleta()
    {
        $in = self::getCaseData('boleta');
        $Invoice = new DataMap($in, Catalogo::DOCTYPE_BOLETA);
        $out = [
            'currencyCode'        => $Invoice->getCurrencyCode(),
            'operationType'       => $Invoice->getOperationType(),
            'documentSeries'      => $Invoice->getDocumentSeries(),
            'documentNumber'      => (int)$Invoice->getDocumentNumber(),
            'customerDocType'     => $Invoice->getCustomerDocType(),
            'customerDocNumber'   => $Invoice->getCustomerDocNumber(),
            'customerRegName'     => $Invoice->getCustomerRegName(),
            'customerAddress'     => $Invoice->getCustomerAddress(),
            'issueDate'           => $Invoice->getIssueDate()->format('Y-m-d\TH:i:s'),
            'allowancesCharges'   => $Invoice->getAllowancesAndCharges(),
            'items'               => $Invoice->getRawItems(),
            'totalTaxes'          => (float)Operations::formatAmount($Invoice->getTotalTaxes()),
            'totalFreeOperations' => (float)Operations::formatAmount($Invoice->getTotalFreeOperations()),
            'taxableAmount'       => (float)Operations::formatAmount($Invoice->getTaxableAmount()),
            'totalAllowances'     => (float)Operations::formatAmount($Invoice->getTotalAllowances()),
            'payableAmount'       => (float)Operations::formatAmount($Invoice->getPayableAmount())
        ];
        unset($in['documentSeries']);
        unset($out['documentSeries']);
        self::assertEquals($in, $out);
    }

    public function generateDetailMatrix($caseName)
    {
        $data = self::getCaseData($caseName);
        $Items = new InvoiceItems();
        $Items->populate($data['items'], 'PEN');
        // Calculate totals
        $rows = $Items->countRows();
        $Items->set(InvoiceItems::COL_IGV,                 $rows, $Items->sum(InvoiceItems::COL_IGV));
        $Items->set(InvoiceItems::COL_ITEM_BILLABLE_VALUE, $rows, $Items->sum(InvoiceItems::COL_ITEM_BILLABLE_VALUE));
        $Items->set(InvoiceItems::COL_ITEM_PAYABLE_AMOUNT, $rows, $Items->sum(InvoiceItems::COL_ITEM_PAYABLE_AMOUNT));
        $Items->set(InvoiceItems::COL_ITEM_TAXABLE_AMOUNT, $rows, $Items->sum(InvoiceItems::COL_ITEM_TAXABLE_AMOUNT));

        $html = $Items->getHtml();
        file_put_contents(__DIR__ . "/cases/$caseName.html", $html);
    }

    public function testGetCatItem()
    {
        $output = Catalogo::getCatItem(16, '01');
        $expected = [
            'id' => '01',
            'value' => 'Precio unitario (incluye el IGV)'
        ];
        self::assertEquals($expected, $output);
    }

    public static function getCaseData($caseName)
    {
        return require "cases/$caseName.php";
    }
}
