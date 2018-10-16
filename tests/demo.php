<?php

require __DIR__ . '/../vendor/autoload.php';

use F72X\F72X;
use F72X\Sunat\DocumentGenerator;
use F72X\Sunat\ServiceGateway;

/**
 * =======================
 * 1. CONFIGURAR EL MÓDULO
 * =======================
 */

// Modo producccion: true, para usar los servicios de producciòn de SUNAT.
$prodMode = false;

/**
 * Repositorio digital:
 * Consta de los diguientes subdirectorios:
 *     - bill      : Facturas y Boletas en xml
 *     - signedbill: Facturas firmadas
 *     - zippedbill: Facturas comprimidas listas para ser enviadas a sunat
 *     - crd       : Constancias de recepción
 */
$repoPath = __DIR__ . '/edocs';

/**
 * Directorio de configuración del emisor
 * =======================================
 * Consta de los diguientes subdirectorios:
 *     - certs: Certificados
 *     - lists: Listas personalizadas
 */
$cfgPath   = __DIR__ . '/companyconfig';

// Nombre del ertificado digital a ser usado para las firmas
$certificate = '20100454523_2018_09_27.pem';
// Datos del emisor
F72X::init([
    'ruc'                   => '20100454523',
    'razonSocial'           => 'Soporte Tecnológicos EIRL',
    'nombreComercial'       => 'Tu Soporte',
    'codigoDomicilioFiscal' => '0000',
    'usuarioSol'            => 'MODDATOS',
    'claveSol'              => 'moddatos',
    'cconfigPath'           => $cfgPath,
    'repoPath'              => $repoPath,
    'certificate'           => $certificate,
    'prodMode'              => $prodMode
]);

echo "1. CONFIGURACIÓN: OK<br>";

// 2. GENERAR FACTURA
// ==================

// data de la factura
$dataFactura = require 'cases/factura_caso1.php';

// generar
$boletaSunat = DocumentGenerator::generateFactura($dataFactura);
echo "2. GENERACIÓN DE FACTURA Y FIRMA: OK<br>";

// 1. ENVIAR A SUNAT
// =================
$billName = $boletaSunat->getBillName();
$response = ServiceGateway::sendBill($billName);
echo "3. ENVIO Y RECEPCION SUNAT: OK";
echo '<pre>';
echo "CDR:";
print_r($response);
echo '</pre>';