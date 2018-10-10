<?php

$dt =  new DateTime();
$dt->setDate(2017, 5, 14);
$dt->setTime(13, 25, 51);
return [
    'operationType'     => '0101',              // Tipo de operación Catálogo #51
    'voucherSeries'     => 1,                   // Serie de la factura
    'voucherNumber'     => 4355,                // Número correlativo de la factura
    'customerDocType'   => '6',                 // Tipo de documento Catálogo #6
    'customerDocNumber' => '20587896411',       // RUC
    'customerRegName'   => 'SERVICABINAS S.A.', // Razón social
    'issueDate'         => $dt,                 // Fecha de emisión [opcional], si no se especifica se usara la fecha del sistema!
    'purchaseOrder'     => 7852166,             // Numero de orden de commpra,
    'allowancesCharges' => [
        [
            'isCharge'         => false,
            'reasonCode'       => '00', // Código de descuento Cátalogo #53
            'multiplierFactor' => 0.05
        ]
    ],
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
            'allowancesCharges'  => [
                [
                    'isCharge'         => false,
                    'reasonCode'       => '00',
                    'multiplierFactor' => 0.1
                ]
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
            'allowancesCharges'  => [
                [
                    'isCharge'         => false,
                    'reasonCode'       => '00',
                    'multiplierFactor' => 0.15
                ]
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
//        [
//            'productCode'        => 'WCG01',
//            'sunatProductCode'   => '45121520',
//            'unitCode'           => 'NIU',
//            'quantity'           => 1,
//            'description'        => 'Web cam Genius iSlim 310',
//            'priceType'          => '02',
//            'taxType'            => '9996',
//            'igvAffectationCode' => '31',
//            'unitValue'          => 30.00,
//            'igvIncluded'        => false
//        ]
    ],
    // Campos calculados solo con fines de validación
    'taxableAmount'   => 348199.15,
    'totalTaxes'      => 62675.85,
    'totalAllowances' => 59230.51,
    'payableAmount'   => 423225.00
];