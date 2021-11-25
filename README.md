# Modulo F72X
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jxcodes/F72X/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jxcodes/F72X/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jxcodes/F72X/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jxcodes/F72X/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/jxcodes/F72X/badges/build.png?b=master)](https://scrutinizer-ci.com/g/jxcodes/F72X/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/jxcodes/F72X/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

Modulo de facturación electrónica SUNAT UBL 2.1
# Referencias:
Manuales SUNAT
[https://cpe.sunat.gob.pe/node/88](https://cpe.sunat.gob.pe/node/88)

# Instalación:
```ruby
composer require jxcodes/f72x
````
# Uso:
```ruby
require 'vendor/autoload.php';

use F72X\F72X;
use F72X\Sunat\DocumentGenerator;
use F72X\Sunat\ServiceGateway;
```
## 1. CONFIGURAR MODULO:
```php
// Modo producccion: true, para usar los servicios de producciòn de SUNAT.
$prodMode = false;

/**
 * Repositorio digital:
 * Consta de los diguientes subdirectorios:
 *     - bill      : Documentos electrónicos en XML
 *     - billinput : Data utilizada para generar el documento electrónico
 *     - signedbill: Documentos electrónicos firmados
 *     - zippedbill: Documentos electrónicos comprimidos y listos para ser enviadas a SUNAT
 *     - crd       : Constancias de recepción
 */
$repoPath = __DIR__ . '/tests/edocs';

/**
 * Directorio de configuración del emisor
 * =======================================
 * Consta de los diguientes subdirectorios:
 *     - certs: Certificados
 *     - lists: Listas personalizadas
 *     - tpls: Templates para formatos de impresión
 */
$cfgPath   = __DIR__ . '/tests/companyconfig';

// Nombre del ertificado digital a ser usado para las firmas
$certificate = '20100454523_2018_09_27.pem';
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
    'cconfigPath'           => $cfgPath,
    'repoPath'              => $repoPath,
    'certificate'           => $certificate,
    'prodMode'              => $prodMode
]);
```
## 2. GENERAR DOCUMENTOS
### FACTURA
```php
// Data
$data = require 'tests/cases/factura.php';
// Procesar Data
$XML = DocumentGenerator::createDocument('FAC', $data);
// Generar Documentos
DocumentGenerator::generateFiles($XML);
// Enviar a SUNAT
$documentName = $xmlFAC->getDocumentName();
$response = ServiceGateway::sendBill($documentName);
// Procesar Respuesta
var_dump($response);
```
### BOLETA DE VENTA
```php
// Data
$data = require 'tests/cases/factura/factura-pago-contado.php';
// Procesar Data
$XML = DocumentGenerator::createDocument('BOL', $data);
// Generar Documentos
DocumentGenerator::generateFiles($XML);
// Enviar a SUNAT
$documentName = $xmlFAC->getDocumentName();
$response = ServiceGateway::sendBill($documentName);
// Procesar Respuesta
var_dump($response);
```

### NOTA DE CRÉDITO
```php
// Data
$data = require 'tests/cases/factura.php';
// Procesar Data
$XML = DocumentGenerator::createDocument('NCR', $data);
// Generar Documentos
DocumentGenerator::generateFiles($XML);
// Enviar a SUNAT
$documentName = $xmlFAC->getDocumentName();
$response = ServiceGateway::sendBill($documentName);
// Procesar Respuesta
var_dump($response);
```

### NOTA DE DÉBITO
```php
// Data
$data = require 'tests/cases/factura.php';
// Procesar Data
$XML = DocumentGenerator::createDocument('NDE', $data);
// Generar Documentos
DocumentGenerator::generateFiles($XML);
// Enviar a SUNAT
$documentName = $xmlFAC->getDocumentName();
$response = ServiceGateway::sendBill($documentName);
// Procesar Respuesta
var_dump($response);
```
