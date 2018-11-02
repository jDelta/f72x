<?php

require 'vendor/autoload.php';

use F72X\F72X;
use F72X\Sunat\DocumentGenerator;
use F72X\Sunat\ServiceGateway;

// INIT
F72X::init([
    'ruc'                   => '20100454523',
    'razonSocial'           => 'Soporte Tecnológicos EIRL',
    'nombreComercial'       => 'Tu Soporte',
    'codigoDomicilioFiscal' => '0000',
    'usuarioSol'            => 'MODDATOS',
    'claveSol'              => 'moddatos',
    'cconfigPath'           => __DIR__ . '/tests/companyconfig',
    'repoPath'              => __DIR__ . '/tests/edocs',
    'certificate'           => '20100454523_2018_09_27.pem',
    'prodMode'              => false
]);

// FACTURA
$dataFAC = require 'tests/cases/factura.php';
$xmlFAC = DocumentGenerator::createDocument('FAC', $dataFAC);
DocumentGenerator::generateFiles($xmlFAC);
$billName = $xmlFAC->getBillName();
$resFAC = ServiceGateway::sendBill($billName);
var_dump($resFAC);

// BOLETA DE VENTA
$dataBOL = require 'tests/cases/boleta.php';
$xmlBOL = DocumentGenerator::createDocument('BOL', $dataBOL);
DocumentGenerator::generateFiles($xmlBOL);
$billName = $xmlBOL->getBillName();
$resBOL = ServiceGateway::sendBill($billName);
var_dump($resBOL);

// NOTA DE CRÉDITO
$dataNCR = require 'tests/cases/notacredito.php';
$xmlNCR = DocumentGenerator::createDocument('NCR', $dataNCR);
DocumentGenerator::generateFiles($xmlNCR);
$billName = $xmlNCR->getBillName();
$resNCR = ServiceGateway::sendBill($billName);
var_dump($resNCR);

// NOTA DE DÉBITO
$dataNDE = require 'tests/cases/notadebito.php';
$xmlNDE = DocumentGenerator::createDocument('NDE', $dataNDE);
DocumentGenerator::generateFiles($xmlNDE);
$billName = $xmlNDE->getBillName();
$resNDE = ServiceGateway::sendBill($billName);
var_dump($resNDE);