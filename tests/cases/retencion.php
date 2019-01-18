<?php

return [
    'documentSeries' => '001',        // Serie de la retención
    'documentNumber' => 123,          // Número correlativo de la factura
    'issueDate'      => '2015-12-23', // Fecha de generación del documento - ISO 8601 date
    'customer'       => [
        'idDocType'         => '6',
        'idDocNumber'       => '20546772439',
        'regName'           => 'CIA. DE CONSULTORIA Y PLANEAMIENTO S.A.C.',
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
    'lines' => [
        [
            'documentType'   => '01',              // Cátalogo #1: Tipo de comprobante
            'documentSeries' => 'F001',            // Serie de comprobante
            'documentNumber' => 1,                 // Número del comprobante
            'voidReason'     => 'Error en sistema' // Motivo de baja
        ],
        [
            'documentType'   => '01',
            'documentSeries' => 'F001',
            'documentNumber' => 15,
            'voidReason'     => 'Cancelación'
        ]
    ]
];