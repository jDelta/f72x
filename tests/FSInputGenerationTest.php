<?php

namespace Tests;

use F72X\Tools\FSInputGenerator;
use PHPUnit\Framework\TestCase;

final class FSInputGenerationTest extends TestCase {

    public function __construct() {
        Util::initF72X();
    }
    public function testGenerarTip() {
        self::assertTrue(true);
    }
    public function XtestGenerarBoleta() {
        $data = Util::getCaseData('boleta_caso1');
        FSInputGenerator::generateBoleta($data, '20100454523');
    }

    public function XtestGenerarFactura() {
        $data = Util::getCaseData('factura_caso1');
        FSInputGenerator::generateFactura($data, '20100454523');
    }

}
