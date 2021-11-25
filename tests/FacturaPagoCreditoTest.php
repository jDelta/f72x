<?php

namespace Tests;

final class FacturaPagoCreditoTest extends FacturaTestCase {

    public function __construct() {
        $data = require __DIR__ . '/cases/facturas/factura-pago-credito.php';
        parent::__construct($data);
    }

    public function testGenerarFactura() {
        $this->generarFactura();
    }

    public function testSendToSunat() {
        $this->sendToSunat();
    }

}
