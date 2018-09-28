<?php

require __DIR__ . '/../vendor/autoload.php';

use F72X\F72X;
use F72X\Sunat\DocumentGenerator;
use F72X\Sunat\ServiceGateway;

// INIICIAR MODULO
F72X::init([
    'RUC'                     => '20100454523',
    'RAZON_SOCIAL'            => 'Soporte Tecnológicos EIRL',
    'NOMBRE_COMERCIAL'        => 'Tu Soporte',
    'USUARIO_SOL'             => 'MODDATOS',
    'CLAVE_SOL'               => 'moddatos',
    'CODIGO_DOMICILIO_FISCAL' => '0000',
    'RUTA_CERTIFICADO'        => __DIR__ . '/cert/20100454523_cert.pem',
    'RUTA_REPOSITORIO'        => __DIR__ . '/repository',
    'MODO_PRODUCCION'         => FALSE
]);

echo "1. CONFIGURACIÓN: OK<br>";
// GENERAR FACTURA

// fecha actual
$dt =  new DateTime();
$dt->setDate(2017, 5, 14);
$dt->setTime(13, 25, 51);

// data de la factura
$dataFactura = [
    'operationTypeCode' => '0101',              // Tipo de operación Catálogo #51
    'voucherSeries'     => 1,                   // Serie de la factura
    'voucherNumber'     => 4355,                // Número correlativo de la factura
    'customerName'      => 'SERVICABINAS S.A.', // Razón social
    'customerDocNumber' => '20587896411',       // RUC
    'customerDocType'   => '6',                 // Tipo de documento Catálogo #6
    'date'              => $dt,                 // Opcional, si no se especifica se usara la fecha del sistema!
    'purchaseOrder'     => 7852166,             // Numero de orden de commpra,
    'allowances'        => [
        ['reasonCode'       => '00', 'multiplierFactor'  => 0.05]
    ],
    'items' => [
        [
            'productCode'           => 'GLG199',    // Código
            'sunatProductCode'      => '52161515',  // Código de producto SUNAT
            'unitCode'              => 'NIU',       // Código de unidad
            'quantity'              => 2000,        // Cantidad
            'description'           => 'Grabadora LG Externo Modelo: GE20LU10', // Descripción detallada
            'priceTypeCode'         => '01',        // Catálogo #16 [01:Precio Unitario|02:Valor Referencial]
            'taxTypeCode'           => '1000',      // Catálogo #5
            'igvAffectationCode'    => '10',        // Catálogo #7
            'unitValue'             => 98.00,       // Valor unitario
            'igvIncluded'           => true,        // true si el valor unitario incluye IGV
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
            'priceTypeCode'         => '01',
            'taxTypeCode'           => '1000',
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
            'priceTypeCode'         => '01',
            'taxTypeCode'           => '9997',
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
            'priceTypeCode'         => '01',
            'taxTypeCode'           => '1000',
            'igvAffectationCode'    => '10',
            'unitValue'             => 196.00,
            'igvIncluded'           => true
        ]
    ]
];

// generar
DocumentGenerator::generateFactura($dataFactura);
echo "2. GENERACIÓN DE FACTURA Y FIRMA: OK<br>";

// ENVIAR A SUNAT
ServiceGateway::sendBill('20100454523-01-F001-00004355.zip');
echo "2. ENVIO Y RECEPCION SUNAT: OK<br>";