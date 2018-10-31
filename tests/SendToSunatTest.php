<?php

namespace Tests;

use F72X\Sunat\ServiceGateway;
use PHPUnit\Framework\TestCase;

final class SendToSunatTest extends TestCase {

    public function __construct() {
        Util::initF72X();
    }

    public static function testSendFacturaCase1() {
        $expected = [
            'responseCode' => '0',
            'responseDesc' => 'La Factura numero F001-00004355, ha sido aceptada'
        ];
        $response = ServiceGateway::sendBill('20100454523-01-F001-00004355');
        $actual = [
            'responseCode' => $response['responseCode'],
            'responseDesc' => $response['responseDesc']
        ];
        self::assertEquals($expected, $actual);
    }

    public static function testSendBoletaCase1() {
        $expected = [
            'responseCode' => '0',
            'responseDesc' => 'La Boleta numero B001-00003652, ha sido aceptada'
        ];
        $response = ServiceGateway::sendBill('20100454523-03-B001-00003652');
        $actual = [
            'responseCode' => $response['responseCode'],
            'responseDesc' => $response['responseDesc']
        ];
        self::assertEquals($expected, $actual);
    }

    public static function testSendNotaCreditoCase1() {
        $expected = [
            'responseCode' => '0',
            'responseDesc' => 'La Nota de Credito numero FC01-00000211, ha sido aceptada'
        ];
        $response = ServiceGateway::sendBill('20100454523-07-FC01-00000211');
        $actual = [
            'responseCode' => $response['responseCode'],
            'responseDesc' => $response['responseDesc']
        ];
        self::assertEquals($expected, $actual);
    }

}
