<?php

namespace Tests;

use F72X\Tools\PdfGenerator;
use F72X\Sunat\DataMap;
use F72X\Sunat\Catalogo;
use F72X\Repository;
use PHPUnit\Framework\TestCase;

final class PdfGeneratorTest extends TestCase {

    protected function setUp() {
        Util::initModule();
    }

    public function testGen() {
        $data = Util::getCaseData('factura');
        $Invoice = new DataMap($data, Catalogo::DOCTYPE_FACTURA);
        $documentName = $Invoice->getDocumentName();
        Repository::removeFile(Repository::getPdfPath($documentName), false);
        PdfGenerator::generatePdf($Invoice, $documentName);
    }

    /**
     * 
     * @param type $param
     */
    public function XtestPrintInvoiceHTMLInput() {
        $input = Util::getCaseData('factura');
        $dataMap = new DataMap($input, Catalogo::DOCTYPE_FACTURA);
        $out = PdfGenerator::getRenderedHtml($dataMap, 'factura.html');
        file_put_contents(__DIR__.'/factura.html', $out);
    }
    /**
     * 
     * @param type $param
     */
    public function XtestPrintCreditNoteHTMLInput() {
        $input = Util::getCaseData('notacredito');
        $dataMap = new DataMap($input, Catalogo::DOCTYPE_NOTA_CREDITO);
        $out = PdfGenerator::getRenderedHtml($dataMap, 'nota-credito.html');
        file_put_contents(__DIR__.'/nota-credito.html', $out);
    }
}
