<?php

namespace Tests;


use F72X\Sunat\Operations;
use F72X\Sunat\Catalogo;
use F72X\Sunat\DocumentGenerator;
use F72X\Sunat\InvoiceDocument;
use F72X\Sunat\InvoiceItems;

use PHPUnit\Framework\TestCase;

final class InvoiceGenerationTest extends TestCase {

    public function __construct() {
        Util::initF72X();
        $this->generateDetailMatrix('boleta_caso1');
        $this->generateDetailMatrix('factura_caso1');
    }

    public function testGenerateFactura() {
        $data = self::getCaseData('factura_caso1');
        DocumentGenerator::generateFactura($data);
    }

    public function testGeneratBoleta() {
        $data = self::getCaseData('boleta_caso1');
        DocumentGenerator::generateBoleta($data);
    }

    public function testInvoiceDocumentRightCalcsForFactura() {
        $in = self::getCaseData('factura_caso1');
        $Invoice = new InvoiceDocument($in, 'B');
        $out = [
                'operationType'     => $Invoice->getOperationType(),
                'voucherSeries'     => $Invoice->getVoucherSeries(),
                'voucherNumber'     => $Invoice->getVoucherNumber(),
                'customerDocType'   => $Invoice->getCustomerDocType(),
                'customerDocNumber' => $Invoice->getCustomerDocNumber(),
                'customerRegName'   => $Invoice->getCustomerRegName(),
                'issueDate'         => $Invoice->getIssueDate(),
                'purchaseOrder'     => $Invoice->getPurchaseOrder(),
                'allowances'        => $Invoice->getAllowances(),
                'charges'           => $Invoice->getCharges(),
                'items'             => $Invoice->getRawItems(),
                'totalTaxes'        => (float)Operations::formatAmount($Invoice->getTotalTaxes()),
                'taxableAmount'     => (float)Operations::formatAmount($Invoice->getTaxableAmount()),
                'totalAllowances'   => (float)Operations::formatAmount($Invoice->getTotalAllowances()),
                'payableAmount'     => (float)Operations::formatAmount($Invoice->getPayableAmount())
        ];
        self::assertEquals($in, $out);
    }

    public function testInvoiceDocumentRightCalcsForBoleta() {
        $in = self::getCaseData('boleta_caso1');
        $Invoice = new InvoiceDocument($in, 'B');
        $out = [
                'operationType'       => $Invoice->getOperationType(),
                'voucherSeries'       => $Invoice->getVoucherSeries(),
                'voucherNumber'       => $Invoice->getVoucherNumber(),
                'customerDocType'     => $Invoice->getCustomerDocType(),
                'customerDocNumber'   => $Invoice->getCustomerDocNumber(),
                'customerRegName'     => $Invoice->getCustomerRegName(),
                'issueDate'           => $Invoice->getIssueDate(),
                'allowances'          => $Invoice->getAllowances(),
                'charges'             => $Invoice->getCharges(),
                'items'               => $Invoice->getRawItems(),
                'totalTaxes'          => (float)Operations::formatAmount($Invoice->getTotalTaxes()),
                'totalFreeOperations' => (float)Operations::formatAmount($Invoice->getTotalFreeOperations()),
                'taxableAmount'       => (float)Operations::formatAmount($Invoice->getTaxableAmount()),
                'totalAllowances'     => (float)Operations::formatAmount($Invoice->getTotalAllowances()),
                'payableAmount'       => (float)Operations::formatAmount($Invoice->getPayableAmount())
        ];
        self::assertEquals($in, $out);
    }

    public function generateDetailMatrix($caseName) {
        $data = self::getCaseData($caseName);
        $Items = new InvoiceItems();
        $Items->populate($data['items'], 'PEN');
        // Calculate totals
        $rows = $Items->countRows();
        $Items->set(InvoiceItems::COL_IGV,   $rows, $Items->sum(InvoiceItems::COL_IGV));
        $Items->set(InvoiceItems::COL_ALLOWANCES, $rows, $Items->sum(InvoiceItems::COL_ALLOWANCES));
        $Items->set(InvoiceItems::COL_ITEM_BILLABLE_VALUE, $rows, $Items->sum(InvoiceItems::COL_ITEM_BILLABLE_VALUE));
        $Items->set(InvoiceItems::COL_ITEM_PAYABLE_AMOUNT, $rows, $Items->sum(InvoiceItems::COL_ITEM_PAYABLE_AMOUNT));
        $Items->set(InvoiceItems::COL_ITEM_TAXABLE_AMOUNT, $rows, $Items->sum(InvoiceItems::COL_ITEM_TAXABLE_AMOUNT));
        
        $html = $Items->getHtml();
        file_put_contents(__DIR__ . "/cases/$caseName.html", $html);
    }

    public function testGetCatItem() {
        $output = Catalogo::getCatItem(16, '01');
        $expected = [
            'id'    => '01',
            'value' => 'Precio unitario (incluye el IGV)'
        ];
        self::assertEquals($expected, $output);
    }

    public static function getCaseData($caseName) {
        return require "cases/$caseName.php";
    }
}
