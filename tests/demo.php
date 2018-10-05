<?php

require __DIR__ . '/../vendor/autoload.php';

use F72X\F72X;
use F72X\Sunat\DocumentGenerator;
use F72X\Sunat\ServiceGateway;

// 1. CONFIGURAR EL MÓDULO
// =======================
$prodMode   = false; // true, para usar los servicios de producciòn de SUNAT.
$certPath   = __DIR__ . '/cert/20100454523_cert.pem'; // Ruta del certificado digital.
$repository = __DIR__ . '/repository';                // Directorio donde se guadaràn
                                                      // las facturas, deberà contener
                                                      // los siguientes subdirectorios:
                                                      // (cdr, sxml, sml y zip).
// Datos del emisor
F72X::init([
    'RUC'                     => '20100454523',
    'RAZON_SOCIAL'            => 'Soporte Tecnológicos EIRL',
    'NOMBRE_COMERCIAL'        => 'Tu Soporte',
    'USUARIO_SOL'             => 'MODDATOS',
    'CLAVE_SOL'               => 'moddatos',
    'CODIGO_DOMICILIO_FISCAL' => '0000',
    'RUTA_CERTIFICADO'        => $certPath,
    'RUTA_REPOSITORIO'        => $repository
], $prodMode);

echo "1. CONFIGURACIÓN: OK<br>";

// 2. GENERAR FACTURA
// ==================

// fecha
$dt =  new DateTime();
$dt->setDate(2017, 5, 14);
$dt->setTime(13, 25, 51);

// data de la factura
$dataFactura = [
    'operationTypeCode' => '0101',              // Tipo de operación Catálogo #51.
    'voucherSeries'     => 1,                   // Serie de la factura.
    'voucherNumber'     => 4355,                // Número correlativo de la factura.
    'customerName'      => 'SERVICABINAS S.A.', // Razón social del receptor.
    'customerDocNumber' => '20587896411',       // RUC del receptor.
    'customerDocType'   => '6',                 // Tipo de documento del receptor Catálogo #6.
    'date'              => $dt,                 // Opcional, por defecto usará la fecha y hora del sistema.
    'purchaseOrder'     => 7852166,             // Opcional, numero de orden de commpra.
    'allowances'        => [
        ['reasonCode'       => '00', 'multiplierFactor'  => 0.05]
    ],
    'items' => [
        [
            'productCode'           => 'GLG199',    // Código.
            'sunatProductCode'      => '52161515',  // Código de producto SUNAT. Catálogo #25.
            'unitCode'              => 'NIU',       // Código de unidad. Catálogo #3.
            'quantity'              => 2000,        // Cantidad.
            'description'           => 'Grabadora LG Externo Modelo: GE20LU10', // Descripción.
            'priceType'         => '01',        // Catálogo #16.
            'taxType'           => '1000',      // Catálogo #5.
            'igvAffectationCode'    => '10',        // Catálogo #7.
            'unitValue'             => 98.00,       // Valor unitario.
            'igvIncluded'           => true,        // true si el IGV está incluido en el Valor unitario.
            'allowances'            => [
                ['reasonCode' => '00', 'multiplierFactor'  => 0.1]
            ]
        ],
        [
            'productCode'           => 'MVS546',
            'sunatProductCode'      => '43211902',
            'unitCode'              => 'NIU',
            'quantity'              => 300,
            'description'           => 'Monitor LCD ViewSonic VG2028WM 20',
            'priceType'         => '01',
            'taxType'           => '1000',
            'igvAffectationCode'    => '10',
            'unitValue'             => 620.00,
            'igvIncluded'           => true,
            'allowances'            => [
                ['reasonCode' => '00', 'multiplierFactor'  => 0.15]
            ]
        ],
        [
            'productCode'           => 'MPC35',
            'sunatProductCode'      => '43202010',
            'unitCode'              => 'NIU',
            'quantity'              => 250,
            'description'           => 'Memoria DDR-3 B1333 Kingston',
            'priceType'         => '01',
            'taxType'           => '9997',
            'igvAffectationCode'    => '20',
            'unitValue'             => 52.00,
            'igvIncluded'           => false
        ],
        [
            'productCode'           => 'TMS22',
            'sunatProductCode'      => '43211706',
            'unitCode'              => 'NIU',
            'quantity'              => 500,
            'description'           => 'Teclado Microsoft SideWinder X6',
            'priceType'         => '01',
            'taxType'           => '1000',
            'igvAffectationCode'    => '10',
            'unitValue'             => 196.00,
            'igvIncluded'           => true
        ]
    ]
];

// generar
DocumentGenerator::generateFactura($dataFactura);
echo "2. GENERACIÓN DE FACTURA Y FIRMA: OK<br>";

// 1. ENVIAR A SUNAT
// =================
ServiceGateway::sendBill('20100454523-01-F001-00004355.zip');
echo "2. ENVIO Y RECEPCION SUNAT: OK<br>";