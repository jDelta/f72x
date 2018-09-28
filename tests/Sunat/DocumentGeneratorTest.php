<?php

namespace Tests\Sunat;


use DateTime;
use F72X\F72X;
use F72X\Company;
use F72X\Sunat\CurrencyOperations;
use F72X\Sunat\SunatVars;
use F72X\Sunat\Catalogo;
use F72X\Sunat\DocumentGenerator;
use F72X\Sunat\DetailMatrix;
use F72X\Sunat\ServiceGateway;

use PHPUnit\Framework\TestCase;

final class DocumentGeneratorTest extends TestCase {
    private $dataFactura;
    public function __construct() {
        F72X::init([
            'RUC'                     => '20100454523',
            'RAZON_SOCIAL'            => 'Soporte Tecnológicos EIRL',
            'NOMBRE_COMERCIAL'        => 'Tu Soporte',
            'USUARIO_SOL'             => 'MODDATOS',
            'CLAVE_SOL'               => 'moddatos',
            'CODIGO_DOMICILIO_FISCAL' => '0000',
            'RUTA_CERTIFICADO'        => __DIR__.'/../cert/20100454523_cert.pem',
            'RUTA_REPOSITORIO'        => __DIR__.'/../repository',
            'MODO_PRODUCCION'         => FALSE
        ]);
        $dt =  new DateTime();
        $dt->setDate(2017, 5, 14);
        $dt->setTime(13, 25, 51);
        $this->dataFactura = [
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
//                [
//                    'productCode'           => 'WCG01',
//                    'sunatProductCode'      => '45121520',
//                    'unitCode'              => 'NIU',
//                    'quantity'              => 1,
//                    'description'           => 'Web cam Genius iSlim 310',
//                    'priceTypeCode'         => '02',
//                    'taxTypeCode'           => '9998',
//                    'igvAffectationCode'    => '30',
//                    'unitValue'             => 30.00,
//                    'igvIncluded'           => false
//                ]
            ]
        ];
    }

    public function testGenerateFactura() {
        DocumentGenerator::generateFactura($this->dataFactura);
    }

    public function testGenerateJsonForFacturadorSunat() {
        $data = $this->dataFactura;
        $dt = $data['date'];
        $dMatrix = new DetailMatrix();
        $dMatrix->populate($data['items'], 'PEN');
        $json = [
            'cabecera' => [
                'tipOperacion'      => $data['operationTypeCode'],
                'fecEmision'        => $dt->format('Y-m-d'),
                'horEmision'        => $dt->format('H:i:s'),
                'fecVencimiento'    => '-',
                'codLocalEmisor'    => Company::getRegAddressCode(),
                'tipDocUsuario'     => $data['customerDocType'],
                'numDocUsuario'     => $data['customerDocNumber'],
                'rznSocialUsuario'  => $data['customerName'],
                'tipMoneda'         => 'PEN',
                
                'sumTotTributos'    => 62675.85,
                'sumTotValVenta'    => 419779.66,
                'sumPrecioVenta'    => 423225.00,
                'sumDescTotal'      => 59230.51,
                'sumOtrosCargos'    => 0.00,
                'sumTotalAnticipos' =>0.00,
                'sumImpVenta'       => 423225.00,

                'ublVersionId'      => '2.1',
                'customizationId'   => '2.0'
            ],
            'detalle' => []
        ];
        for ($rowIndex = 0; $rowIndex < count($data['items']); $rowIndex++) {
            $cat5Item = Catalogo::getCatItem(5, $dMatrix->getTaxTypeCode($rowIndex));
            $item = [
                'codUnidadMedida'       => $dMatrix->getUnitCode($rowIndex),
                'ctdUnidadItem'         => $dMatrix->getQunatity($rowIndex),
                'codProducto'           => $dMatrix->getProductCode($rowIndex),
                'codProductoSUNAT'      => $dMatrix->getUNPSC($rowIndex),
                'desItem'               => $dMatrix->getDescription($rowIndex),
                'mtoValorUnitario'      => CurrencyOperations::formatAmount($dMatrix->getUnitBillableValue($rowIndex)),
                'sumTotTributosItem'    => CurrencyOperations::formatAmount($dMatrix->getIgv($rowIndex)),
                'codTriIGV'             => $dMatrix->getTaxTypeCode($rowIndex),
                'mtoIgvItem'            => CurrencyOperations::formatAmount($dMatrix->getIgv($rowIndex)),
                'mtoBaseIgvItem'        => CurrencyOperations::formatAmount($dMatrix->getTaxableAmount($rowIndex)),
                'nomTributoIgvItem'     => $cat5Item['name'],
                'codTipTributoIgvItem'  => $cat5Item['UN_ECE_5153'],
                'tipAfeIGV'             => $dMatrix->getIgvAffectationCode($rowIndex),
                'porIgvItem'            => CurrencyOperations::formatAmount(SunatVars::IGV_PERCENT),
                'codTriISC'             => '-',
                'mtoIscItem'            => '0.00',
                'mtoBaseIscItem'        => '0.00',
                'nomTributoIscItem'     => '0.00',
                'codTipTributoIscItem'  => '0.00',
                'tipSisISC'             => '0.00',
                'porIscItem'            => '0.00',
                'codTriOtroItem'        => '-',
                'mtoTriOtroItem'        => '0.00',
                'mtoBaseTriOtroItem'    => '0.00',
                'nomTributoIOtroItem'   => '0.00',
                'codTipTributoIOtroItem'        => '0.00',
                'porTriOtroItem'                => '0.00',
                'mtoPrecioVentaUnitario'        => CurrencyOperations::formatAmount($dMatrix->getUnitPayableAmount($rowIndex)),
                'mtoValorVentaItem'             => CurrencyOperations::formatAmount($dMatrix->getItemBillableValue($rowIndex)),
                'mtoValorReferencialUnitario'   => '0.00',
            ];
            $json['detalle'][] = $item;
        }
        $facturadorSUNATDataDir = 'F:\SUNAT/SFS_v1.2/sunat_archivos/sfs/DATA';
                // Line jump
        $ENTER = chr(13) . chr(10);
        $cabContent = implode('|', $json['cabecera']);

        $detContent = '';
        for ($rowIndex = 0; $rowIndex < count($data['items']); $rowIndex++) {
            $detContent .= implode('|', $json['detalle'][$rowIndex]) . $ENTER;
        }
        file_put_contents("$facturadorSUNATDataDir/20393948125-01-F001-00004355.CAB", $cabContent);
        file_put_contents("$facturadorSUNATDataDir/20393948125-01-F001-00004355.DET", $detContent);

        file_put_contents("00004355.json", json_encode($json));
    }
    public function testDetailMatrixGeneration() {
        $dMatrix = new DetailMatrix();
        $dMatrix->populate($this->dataFactura['items'], 'PEN');
        // Calculate totals
        $rows = $dMatrix->countRows();
        $dMatrix->set(DetailMatrix::COL_IGV,   $rows, $dMatrix->sum(DetailMatrix::COL_IGV));
        $dMatrix->set(DetailMatrix::COL_ALLOWANCES, $rows, $dMatrix->sum(DetailMatrix::COL_ALLOWANCES));
        $dMatrix->set(DetailMatrix::COL_ITEM_BILLABLE_VALUE, $rows, $dMatrix->sum(DetailMatrix::COL_ITEM_BILLABLE_VALUE));
        $dMatrix->set(DetailMatrix::COL_ITEM_PAYABLE_AMOUNT, $rows, $dMatrix->sum(DetailMatrix::COL_ITEM_PAYABLE_AMOUNT));
        $dMatrix->set(DetailMatrix::COL_ITEM_TAXABLE_AMOUNT, $rows, $dMatrix->sum(DetailMatrix::COL_ITEM_TAXABLE_AMOUNT));
        
        $html = $dMatrix->getHtml();
        file_put_contents(__DIR__ . '/DetailMatrixGeneration.html', $html);
    }
    
    public function testDetailMatrixSumaOperacionesGravadas() {
        $dMatrix = new DetailMatrix();
        $dMatrix->populate($this->dataFactura['items'], 'PEN');
        $amount = $dMatrix->getTotalTaxableOperations();
        self::assertEquals($amount, 366525.42372881359);
    }

    public static function testSendBill() {
        ServiceGateway::sendBill('20100454523-01-F001-00004355.zip');
    }
}
