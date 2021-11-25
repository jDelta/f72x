<?php

namespace Tests;

//use F72X\Tools\FSInputGenerator;

final class FSInputGenerationTest extends \PHPUnit_Framework_TestCase {

    public function __construct() {
        Util::initModule();
    }
    public function testTrueIsTrue() {
        self::assertTrue(true);
    }
/*     public function XtestGenerarBoleta() {
        $data = Util::getCaseData('boleta');
        FSInputGenerator::generateBoleta($data, '20100454523');
    }
    public function testGenerarFactura() {
        $data = Util::getCaseData('facturas/factura-pago-contado');
        FSInputGenerator::generateFactura($data, '20100454523');
    } */
}
