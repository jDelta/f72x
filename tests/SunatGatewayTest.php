<?php

namespace Tests;

use F72X\Sunat\ServiceGateway;

final class SunatGatewayTest extends \PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        date_default_timezone_set('America/Lima');
        Util::initModule();
    }

    public function testSendBoletaCase1()
    {
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

    public function testSendCreditNoteCase1()
    {
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

    public function testSendDebitNoteCase1()
    {
        $expected = [
            'responseCode' => '0',
            'responseDesc' => 'La Nota de Debito numero FD01-00000211, ha sido aceptada'
        ];
        $response = ServiceGateway::sendBill('20100454523-08-FD01-00000211');
        $actual = [
            'responseCode' => $response['responseCode'],
            'responseDesc' => $response['responseDesc']
        ];
        self::assertEquals($expected, $actual);
    }

    public static function testSendResumenDiario()
    {
        $ticket = ServiceGateway::sendSummary('20100454523-RC-20171118-00001');
        echo $ticket;
    }
    //
    //    public static function testSendComunicacionBaja() {
    //        $ticket = ServiceGateway::sendSummary('20100454523-RA-20110402-00001');
    //        echo $ticket;
    //    }
    //
    //    public static function testSendPercepcion() {
    //        $ticket = ServiceGateway::sendSummary('20100454523-RA-20110402-00001');
    //        echo $ticket;
    //    }
    //
    //    public static function testGetComunicacionBajaStatus() {
    //        $response = ServiceGateway::getStatus('20100454523-RA-20110402-00001');
    //        echo json_encode($response);
    //    }

    public function testGetResumenDiarioStatus()
    {
        $response = ServiceGateway::getStatus('20100454523-RC-20171118-00001');
        echo json_encode($response);
    }

    public function testGetTicket()
    {
        $xmlString = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
    <soap-env:Header/>
    <soap-env:Body>
        <br:sendSummaryResponse xmlns:br="http://service.sunat.gob.pe">
            <ticket>1542230447563</ticket>
        </br:sendSummaryResponse>
    </soap-env:Body>
</soap-env:Envelope>
XML;
        $xmlObj = simplexml_load_string($xmlString);
        $ticket = (string) $xmlObj->xpath("//ticket")[0];
        self::assertEquals('1542230447563', $ticket);
    }
}
