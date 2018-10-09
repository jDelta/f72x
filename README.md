# Modulo F72X
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jDelta/F72X/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jDelta/F72X/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jDelta/F72X/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jDelta/F72X/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/jDelta/F72X/badges/build.png?b=master)](https://scrutinizer-ci.com/g/jDelta/F72X/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/jDelta/F72X/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

Modulo de facturación electrónica SUNAT UBL 2.1

# Instalación:
```ruby
composer require jdelta/f72x
````

# Uso:
```php
require __DIR__ . '/../vendor/autoload.php';

use F72X\F72X;
use F72X\Sunat\DocumentGenerator;
use F72X\Sunat\ServiceGateway;

// 1. CONFIGURAR EL MÓDULO
// =======================
$prodMode = false; // true, para usar los servicios de producciòn de SUNAT.
$certPath = __DIR__ . '/certs/20100454523_2018_09_27.pem'; // Ruta del certificado digital.
$repoPath = __DIR__ . '/bills'; // Directorio donde se guadarán las facturas,
                                // deberá contener los siguientes subdirectorios:
                                // (cdr, sxml, sml y zip).
// Datos del emisor
F72X::init([
    'ruc'                   => '20100454523',
    'razonSocial'           => 'Soporte Tecnológicos EIRL',
    'nombreComercial'       => 'Tu Soporte',
    'codigoDomicilioFiscal' => '0000',
    'usuarioSol'            => 'MODDATOS',
    'claveSol'              => 'moddatos',
    'certPath'              => $certPath,
    'repoPath'              => $repoPath,
    'prodMode'              => $prodMode
]);

echo "1. CONFIGURACIÓN: OK<br>";

// 2. GENERAR FACTURA
// ==================

// fecha
$dt =  new DateTime();
$dt->setDate(2017, 5, 14);
$dt->setTime(13, 25, 51);

// data de la factura
$dataFactura = [
    'operationType'     => '0101',              // Tipo de operación Catálogo #51
    'voucherSeries'     => 1,                   // Serie de la factura
    'voucherNumber'     => 4355,                // Número correlativo de la factura
    'customerDocType'   => '6',                 // Tipo de documento Catálogo #6
    'customerDocNumber' => '20587896411',       // RUC
    'customerRegName'   => 'SERVICABINAS S.A.', // Razón social
    'issueDate'         => $dt,                 // Fecha de emisión [opcional], si no se especifica se usara la fecha del sistema!
    'purchaseOrder'     => 7852166,             // Numero de orden de commpra,
    'allowances'        => [
        [
            'reasonCode'       => '00', // Código de descuento Cátalogo #53
            'multiplierFactor' => 0.05
        ]
    ],
    'charges'           => [],
    'items' => [
        [
            'productCode'        => 'GLG199',    // Código
            'sunatProductCode'   => '52161515',  // Código de producto SUNAT
            'unitCode'           => 'NIU',       // Código de unidad
            'quantity'           => 2000,        // Cantidad
            'description'        => 'Grabadora LG Externo Modelo: GE20LU10', // Descripción detallada
            'priceType'          => '01',        // Catálogo #16 [01:Precio Unitario|02:Valor Referencial]
            'taxType'            => '1000',      // Catálogo #5
            'igvAffectationCode' => '10',        // Catálogo #7
            'unitValue'          => 98.00,       // Valor unitario
            'igvIncluded'        => true,        // true si el valor unitario incluye IGV
            'allowances'         => [
                ['reasonCode' => '00', 'multiplierFactor'  => 0.1]
            ]
        ],
        [
            'productCode'        => 'MVS546',
            'sunatProductCode'   => '43211902',
            'unitCode'           => 'NIU',
            'quantity'           => 300,
            'description'        => 'Monitor LCD ViewSonic VG2028WM 20',
            'priceType'          => '01',
            'taxType'            => '1000',
            'igvAffectationCode' => '10',
            'unitValue'          => 620.00,
            'igvIncluded'        => true,
            'allowances'         => [
                ['reasonCode' => '00', 'multiplierFactor'  => 0.15]
            ]
        ],
        [
            'productCode'        => 'MPC35',
            'sunatProductCode'   => '43202010',
            'unitCode'           => 'NIU',
            'quantity'           => 250,
            'description'        => 'Memoria DDR-3 B1333 Kingston',
            'priceType'          => '01',
            'taxType'            => '9997',
            'igvAffectationCode' => '20',
            'unitValue'          => 52.00,
            'igvIncluded'        => false
        ],
        [
            'productCode'        => 'TMS22',
            'sunatProductCode'   => '43211706',
            'unitCode'           => 'NIU',
            'quantity'           => 500,
            'description'        => 'Teclado Microsoft SideWinder X6',
            'priceType'          => '01',
            'taxType'            => '1000',
            'igvAffectationCode' => '10',
            'unitValue'          => 196.00,
            'igvIncluded'        => true
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
````
