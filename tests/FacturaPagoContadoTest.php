<?php

namespace Tests;

final class FacturaPagoContadoTest extends FacturaTestCase {

    public function __construct() {
        $data = require __DIR__ . '/cases/facturas/factura-pago-contado.php';
        parent::__construct($data);
    }

    public function testGenerarFactura() {
        $this->generarFactura();
    }

    public function testSendToSunat() {
        $this->sendToSunat();
    }

}
