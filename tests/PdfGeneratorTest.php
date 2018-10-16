<?php

namespace Tests;

use F72X\Tools\PdfGenerator;
use F72X\Sunat\InvoiceDocument;
use F72X\Sunat\Catalogo;
use PHPUnit\Framework\TestCase;

final class PdfGeneratorTest extends TestCase {

    protected function setUp() {
        Util::initF72X();
    }

    public function testGen() {
        $data = Util::getCaseData('factura_caso1');
        $Invoice = new InvoiceDocument($data, Catalogo::CAT1_FACTURA);
        $billName = $Invoice->getBillName();
        PdfGenerator::generateFactura($Invoice, $billName);
    }

}
