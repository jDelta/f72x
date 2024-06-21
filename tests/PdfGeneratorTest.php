<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use F72X\Tools\PdfGenerator;
use F72X\Sunat\DataMap;
use F72X\Sunat\Catalogo;
use F72X\Repository;

final class PdfGeneratorTest extends TestCase
{

    public function testGenerarFacturaContado()
    {
        $this->expectNotToPerformAssertions();
        $data = Util::getCaseData('facturas/factura-pago-contado');
        $Invoice = new DataMap($data, Catalogo::DOCTYPE_FACTURA);
        $documentName = $Invoice->getDocumentName();
        Repository::removeFile(Repository::getPdfPath($documentName), false);
        PdfGenerator::generatePdf($Invoice, $documentName);
    }
    public function testGenerarFacturaCredito()
    {
        $this->expectNotToPerformAssertions();
        $data = Util::getCaseData('facturas/factura-pago-credito');
        $Invoice = new DataMap($data, Catalogo::DOCTYPE_FACTURA);
        $documentName = $Invoice->getDocumentName();
        Repository::removeFile(Repository::getPdfPath($documentName), false);
        PdfGenerator::generatePdf($Invoice, $documentName);
    }

    public function XtestPrintInvoiceHTMLInput()
    {
        $input = Util::getCaseData('facturas/factura-pago-contado');
        $dataMap = new DataMap($input, Catalogo::DOCTYPE_FACTURA);
        $out = PdfGenerator::getRenderedHtml($dataMap, 'factura.html');
        file_put_contents(__DIR__ . '/factura.html', $out);
    }

    public function XtestPrintCreditNoteHTMLInput()
    {
        $input = Util::getCaseData('notacredito');
        $dataMap = new DataMap($input, Catalogo::DOCTYPE_NOTA_CREDITO);
        $out = PdfGenerator::getRenderedHtml($dataMap, 'nota-credito.html');
        file_put_contents(__DIR__ . '/nota-credito.html', $out);
    }
}
