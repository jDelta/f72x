<?php

require_once 'vendor/autoload.php';

use F72X\F72X;
use F72X\Sunat\DocumentGenerator;
use F72X\Sunat\ServiceGateway;

// INIT
F72X::init([
    'ruc'                   => '20100454523',
    'razonSocial'           => 'Soporte Tecnológicos EIRL',
    'nombreComercial'       => 'Tu Soporte',
    'codigoDomicilioFiscal' => '0000',
    'address'               => 'AV. FCO. BOLOGNESI 854',
    'city'                  => 'LIMA',
    'contactInfo'           => 'Email: ventas@miweb.com',
    'usuarioSol'            => 'MODDATOS',
    'claveSol'              => 'moddatos',
    'cconfigPath'           => __DIR__ . '/tests/companyconfig',
    'repoPath'              => __DIR__ . '/tests/edocs',
    'tempPath'              => __DIR__ . '/tests/temp',
    'certificate'           => 'activecert',
    'prodMode'              => false
]);

$serviceGateway = new ServiceGateway();

// FACTURA
$dataFAC = require_once 'tests/cases/factura.php';
$xmlFAC = DocumentGenerator::createDocument('FAC', $dataFAC);
DocumentGenerator::generateFiles($xmlFAC);
$documentName = $xmlFAC->getDocumentName();
$resFAC = $serviceGateway->sendBill($documentName);
var_dump($resFAC);

// BOLETA DE VENTA
$dataBOL = require_once 'tests/cases/boleta.php';
$xmlBOL = DocumentGenerator::createDocument('BOL', $dataBOL);
DocumentGenerator::generateFiles($xmlBOL);
$documentName = $xmlBOL->getDocumentName();
$resBOL = $serviceGateway->sendBill($documentName);
var_dump($resBOL);

// NOTA DE CRÉDITO
$dataNCR = require_once 'tests/cases/notacredito.php';
$xmlNCR = DocumentGenerator::createDocument('NCR', $dataNCR);
DocumentGenerator::generateFiles($xmlNCR);
$documentName = $xmlNCR->getDocumentName();
$resNCR = $serviceGateway->sendBill($documentName);
var_dump($resNCR);

// NOTA DE DÉBITO
$dataNDE = require_once 'tests/cases/notadebito.php';
$xmlNDE = DocumentGenerator::createDocument('NDE', $dataNDE);
DocumentGenerator::generateFiles($xmlNDE);
$documentName = $xmlNDE->getDocumentName();
$resNDE = $serviceGateway->sendBill($documentName);
var_dump($resNDE);
