<?php

namespace Tests;

use F72X\Tools\FSInputGenerator;
use PHPUnit\Framework\TestCase;

final class FSInputGenerationTest extends TestCase {

    public function __construct() {
        Util::initModule();
    }
    public function testGenerarTip() {
        self::assertTrue(true);
    }
    public function XtestGenerarBoleta() {
        $data = Util::getCaseData('boleta');
        FSInputGenerator::generateBoleta($data, '20100454523');
    }

    public function testGenerarFactura() {
        $data = Util::getCaseData('factura');
        FSInputGenerator::generateFactura($data, '20100454523');
    }

}
