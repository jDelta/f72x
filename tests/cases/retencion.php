<?php

return [
    'documentSeries' => '001',        // Serie de la retención
    'documentNumber' => 123,          // Número correlativo de la factura
    'issueDate'      => '2015-12-23', // Fecha de generación del documento - ISO 8601 date
    'systemCode'     => '01',         // Regimen de percepcion Cátalogo 23
    'note'           => 'Se emite con facturas del periodo 12/2015', // Observaciones
    'customer'       => [
        'idDocType'         => '6',
        'idDocNumber'       => '20100070970',
        'regName'           => 'SUPERMERCADOS PERUANOS SOCIEDAD ANONIMA O S.P.S.A.',
        'comName'           => 'CIA. DE CONSULTORIA Y PLANEAMIENTO S.A.C.',
        'postalAddress'     => [
            'id'                    => '150130',
            'streetName'            => 'CAL. CALLE MORELLI 181 INT. P-2',
            'citySubdivisionName'   => '',
            'cityName'              => 'LIMA',
            'countrySubentity'      => 'LIMA',
            'district'              => 'SAN BORJA',
            'countryCode'           => 'PE'
        ]
    ],
// Documentos relacionados
    'lines' => [
        [
            'documentType'       => '01',         // Cátalogo #1: Tipo de comprobante
            'documentSeries'     => 'F001',       // Serie de comprobante
            'documentNumber'     => 14,           // Número del comprobante
            'currencyCode'       => 'PEN',        // Tipo de moneda (ISO 4217)
            'issueDate'          => '2015-12-22', // Fecha emision
            'totalInvoiceAmount' => 23000,
            // Pagos
            'payment'            => [
                'number'            => 1,
                'paidAmount'        => 14000,
                'paidDate'          => '2015-12-24'
            ]
        ],
        [
            'documentType'       => '01',
            'documentSeries'     => 'F001',
            'documentNumber'     => 457,
            'currencyCode'       => 'USD',
            'issueDate'          => '2015-12-10',
            'totalInvoiceAmount' => 1000,
            'payment'            => [
                'number'            => 1,
                'paidAmount'        => 1000,
                'paidDate'          => '2015-12-24'
            ],
            'exchangeRate' => 3.25
        ]
    ]
];