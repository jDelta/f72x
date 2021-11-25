<?php

namespace Tests;

use F72X\Repository;
use F72X\Sunat\DocumentGenerator;
use F72X\Sunat\ServiceGateway;

class FacturaTestCase extends \PHPUnit_Framework_TestCase {

    private $data;
    private $xmlDoc;

    public function __construct($data) {
        date_default_timezone_set('America/Lima');
        // Init módule
        Util::initModule();
        $this->data = $data;
        // Crate document
        $this->xmlDoc = DocumentGenerator::createDocument('FAC', $this->data);
    }

    public function generarFactura() {
        // Delete files
        Repository::removeFiles($this->xmlDoc->getDocumentName(), false);
        // Create new ones
        DocumentGenerator::generateFiles($this->xmlDoc);
    }

    public function sendToSunat() {
        $docId = $this->xmlDoc->getID();
        $expected = [
            'responseCode' => '0',
            'responseDesc' => "La Factura numero $docId, ha sido aceptada"
        ];
        $response = ServiceGateway::sendBill($this->xmlDoc->getDocumentName());
        $actual = [
            'responseCode' => $response['responseCode'],
            'responseDesc' => $response['responseDesc']
        ];
        self::assertEquals($expected, $actual);
    }

}
