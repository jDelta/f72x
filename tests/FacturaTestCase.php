<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use F72X\Repository;
use F72X\Sunat\DocumentGenerator;
use F72X\Sunat\ServiceGateway;

class FacturaTestCase extends TestCase
{

    private $data;
    private $xmlDoc;
    private static ServiceGateway $sunatGateway;
    public static function setUpBeforeClass(): void
    {
        self::$sunatGateway = new ServiceGateway();
    }
    protected function setUp(): void
    {
        $this->data = $this->getDocumentData();
        // Crate document
        $this->xmlDoc = DocumentGenerator::createDocument('FAC', $this->data);
    }
    protected function getDocumentData(): array
    {
        return [];
    }

    public function generarFactura()
    {
        // Delete files
        Repository::removeFiles($this->xmlDoc->getDocumentName(), false);
        // Create new ones
        DocumentGenerator::generateFiles($this->xmlDoc);
    }

    public function sendToSunat()
    {
        $docId = $this->xmlDoc->getID();
        $expected = [
            'responseCode' => '0',
            'responseDesc' => "La Factura numero $docId, ha sido aceptada"
        ];
        $response = self::$sunatGateway->sendBill($this->xmlDoc->getDocumentName());
        $actual = [
            'responseCode' => $response['responseCode'],
            'responseDesc' => $response['responseDesc']
        ];
        self::assertEquals($expected, $actual);
    }
}
