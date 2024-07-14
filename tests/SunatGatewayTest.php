<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use F72X\Sunat\ServiceGateway;

final class SunatGatewayTest extends TestCase
{
    // Algunas veces el servicio de SUNAT puede demorar en responder
    // por lo que se ha definido un tiempo de espera de 2 segundos
    // Para ejecutar las siguientes pruebas sin problemas
    // se ha definido un tiempo de espera de 2 segundos
    private $sleepTime = 1;
    private static ServiceGateway $sunatGateway;
    public static function setUpBeforeClass(): void
    {
        self::$sunatGateway = new ServiceGateway();
    }
    private function someSleep()
    {
        sleep($this->sleepTime);
    }
    public function testSendBoletaCase1()
    {
        $expected = [
            'responseCode' => '0',
            'responseDesc' => 'La Boleta numero B001-00003652, ha sido aceptada'
        ];
        $this->someSleep();
        $response = self::$sunatGateway->sendBill('20100454523-03-B001-00003652');
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
        $this->someSleep();
        $response = self::$sunatGateway->sendBill('20100454523-07-FC01-00000211');
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
        $this->someSleep();
        $response = self::$sunatGateway->sendBill('20100454523-08-FD01-00000211');
        $actual = [
            'responseCode' => $response['responseCode'],
            'responseDesc' => $response['responseDesc']
        ];
        self::assertEquals($expected, $actual);
    }

    public function testSendResumenDiario()
    {
        $this->expectNotToPerformAssertions();
        $this->someSleep();
        $ticket = self::$sunatGateway->sendSummary('20100454523-RC-20171118-00001');
        echo $ticket;
    }
    //
    //    public static function testSendComunicacionBaja() {
    //        $ticket = self::$sunatGateway->sendSummary('20100454523-RA-20110402-00001');
    //        echo $ticket;
    //    }
    //
    //    public static function testSendPercepcion() {
    //        $ticket = self::$sunatGateway->sendSummary('20100454523-RA-20110402-00001');
    //        echo $ticket;
    //    }
    //
    //    public static function testGetComunicacionBajaStatus() {
    //        $response = self::$sunatGateway->getStatus('20100454523-RA-20110402-00001');
    //        echo json_encode($response);
    //    }

    public function testGetResumenDiarioStatus()
    {
        $this->expectNotToPerformAssertions();
        $this->someSleep();
        $response = self::$sunatGateway->getStatus('20100454523-RC-20171118-00001');
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
