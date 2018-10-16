<?php

$dt = new DateTime();
$dt->setDate(2017, 6, 24);
$dt->setTime(13, 25, 51);
return [
    'operationType'     => '0101',                      // Tipo de operación Catálogo #51
    'voucherSeries'     => 'B001',                      // Serie de la factura
    'voucherNumber'     => 3652,                        // Número correlativo de la factura
    'customerDocType'   => '1',                         // Tipo de documento Catálogo #6
    'customerDocNumber' => '46237547',                  // DNI
    'customerRegName'   => 'LUANA KARINA PAZOS ATOCHE', // Nombre de cliente
    'customerAddress'   => '485 UNIVERSAL STREET',      // Dirección del cliente
    'issueDate'         => $dt,                         // Opcional, si no se especifica se usara la fecha del sistema!
    'allowancesCharges' => [
        [
            'isCharge'         => false,
            'reasonCode'       => '00', // Código de descuento Cátalogo #53
            'multiplierFactor' => 0.05
        ]
    ],
    'items' => [
        [
            'productCode'        => 'REF564',    // Código
            'sunatProductCode'   => '52141501',  // Código de producto SUNAT
            'unitCode'           => 'NIU',       // Código de unidad
            'quantity'           => 1,           // Cantidad
            'description'        => 'Refrigeradora marca “AXM” no frost de 200 ltrs.', // Descripción detallada
            'priceType'          => '01',        // Catálogo #16 [01:Precio Unitario|02:Valor Referencial]
            'taxType'            => '1000',      // Catálogo #5
            'igvAffectationCode' => '10',        // Catálogo #7
            'unitValue'          => 998.00,      // Valor unitario
            'igvIncluded'        => true         // true si el valor unitario incluye IGV
        ],
        [
            'productCode'        => 'COC124',
            'sunatProductCode'   => '95141606',
            'unitCode'           => 'NIU',
            'quantity'           => 1,
            'description'        => 'Cocina a gas GLP, marca “AXM” de 5 hornillas',
            'priceType'          => '01',
            'taxType'            => '1000',
            'igvAffectationCode' => '10',
            'unitValue'          => 750.00,
            'igvIncluded'        => true
        ]
//        [
//            'productCode'        => 'NOB012',
//            'sunatProductCode'   => '24121803',
//            'unitCode'           => 'NIU',
//            'quantity'           => 10,
//            'description'        => 'Sixpack de gaseosa “Guerené” de 400 ml',
//            'priceType'          => '02',
//            'taxType'            => '9996',
//            'igvAffectationCode' => '31',
//            'unitValue'          => 4.80,
//            'igvIncluded'        => false
//        ]
    ],
    // Campos calculados solo con fines de validación
    'totalTaxes'          => 253.31,
    'totalFreeOperations' => 0,
    'taxableAmount'       => 1407.29,
    'totalAllowances'     => 74.07,
    'payableAmount'       => 1660.60
    
];