<?php

return [
    'documentSeries'    => '001',                       // Serie de la factura
    'documentNumber'    => 3652,                        // Número correlativo de la factura
    'currencyCode'      => 'PEN',                       // Tipo de moneda (ISO 4217)
    'operationType'     => '0101',                      // Tipo de operación Catálogo #51
    'customerDocType'   => '1',                         // Tipo de documento Catálogo #6
    'customerDocNumber' => '46237547',                  // DNI
    'customerRegName'   => 'LUANA KARINA PAZOS ATOCHE  --- ADDITIONAL TEXT IN ORDER TO TEST LONG NAMES BEHAVIOUR---', // Nombre de cliente
    'customerAddress'   => '485 UNIVERSAL STREET',      // Dirección del cliente
    'issueDate'         => '2017-05-14T13:25:51',       // Fecha de emisión - ISO 8601 date
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
            'unspsc'             => '52141501',  // Código de producto SUNAT
            'unitCode'           => 'NIU',       // Código de unidad
            'quantity'           => 1,           // Cantidad
            'description'        => 'Refrigeradora marca “AXM” no frost de 200 ltrs.', // Descripción detallada
            'priceType'          => '01',        // Catálogo #16 [01:Precio Unitario|02:Valor Referencial]
            'taxType'            => '1000',      // Catálogo #5
            'igvAffectationType' => '10',        // Catálogo #7
            'unitPrice'          => 998.00,      // Precio Unitario/Valor refencial
            'igvIncluded'        => true         // true si Precio Unitario incluye IGV
        ],
        [
            'productCode'        => 'COC124',
            'unspsc'             => '95141606',
            'unitCode'           => 'NIU',
            'quantity'           => 1,
            'description'        => 'Cocina a gas GLP, marca “AXM” de 5 hornillas',
            'priceType'          => '01',
            'taxType'            => '1000',
            'igvAffectationType' => '10',
            'unitPrice'          => 750.00,
            'igvIncluded'        => true
        ],
        [
            'productCode'        => 'NOB012',
            'unspsc'             => '24121803',
            'unitCode'           => 'NIU',
            'quantity'           => 10,
            'description'        => 'Sixpack de gaseosa “Guerené” de 400 ml',
            'priceType'          => '02',
            'taxType'            => '9996',
            'igvAffectationType' => '31',
            'unitPrice'          => 4.80,
            'igvIncluded'        => false
        ]
    ],
    // Campos calculados solo con fines de validación
    'totalTaxes'          => 253.31,
    'totalFreeOperations' => 48.0,
    'taxableAmount'       => 1407.29,
    'totalAllowances'     => 74.07,
    'payableAmount'       => 1660.60
    
];