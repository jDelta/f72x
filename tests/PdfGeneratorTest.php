<?php

namespace Tests;

use F72X\Tools\PdfGenerator;
use F72X\Sunat\DataMap;
use F72X\Sunat\Catalogo;
use F72X\Repository;
use PHPUnit\Framework\TestCase;

final class PdfGeneratorTest extends TestCase {

    protected function setUp() {
        Util::initF72X();
    }

    public function testGen() {
        $data = Util::getCaseData('factura_caso1');
        $Invoice = new DataMap($data, Catalogo::DOCTYPE_FACTURA);
        $billName = $Invoice->getBillName();
        Repository::removeFile(Repository::getPdfPath($billName), false);
        PdfGenerator::generateFactura($Invoice, $billName);
    }

}
